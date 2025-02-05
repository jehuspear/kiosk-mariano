// Thermal Label Printer Handler
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to all thermal print buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.thermal-print-btn')) {
            const button = e.target.closest('.thermal-print-btn');
            const items = button.dataset.items.split('<br>');
            printThermalLabels(
                button.dataset.ticket,
                button.dataset.datetime,
                items
            );
        }
    });
});

function printThermalLabels(ticket, datetime, items) {
    // Create a hidden iframe for printing
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    document.body.appendChild(iframe);

    let totalLabels = 0;
    const labelItems = [];
    
    // Process items and create labels array
    items.forEach(item => {
        const match = item.match(/(\d+)\s*x\s*(.*)/);
        if (match) {
            const quantity = parseInt(match[1]);
            const itemDetails = match[2];
            for (let i = 0; i < quantity; i++) {
                totalLabels++;
                labelItems.push({
                    ticket,
                    datetime,
                    count: `${totalLabels}/${totalLabels}`,
                    item: itemDetails
                });
            }
        }
    });

    // Format datetime
    const date = new Date(datetime);
    const formattedDate = date.toLocaleDateString('en-US', {
        month: '2-digit',
        day: '2-digit',
        year: '2-digit'
    }).replace(/\//g, '/');
    const formattedTime = date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    }).toUpperCase();

    // Create the content with all labels
    const content = `
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                @page {
                    margin: 0;
                    size: 30mm 40mm;
                }
                body {
                    margin: 0;
                    padding: 0;
                    font-family: 'Arial Black', 'Helvetica Black', 'Arial Bold', sans-serif;
                    font-weight: 900;
                    width: 40mm;
                }
                .label-container {
                    width: 40mm;
                    margin: 0;
                    padding-top: 5mm;
                }
                .label {
                    width: 40mm;
                    height: 30mm;
                    padding: 3mm;
                    box-sizing: border-box;
                    page-break-inside: avoid;
                    background: white;
                    border: none;
                    margin-bottom: 50mm;
                    position: relative;
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 0.5mm;
                }
                .ticket-number {
                    font-size: 2.5mm;
                }
                .count {
                    font-size: 2.5mm;
                }
                .datetime {
                    font-size: 2mm;
                    margin-bottom: 1mm;
                }
                .item {
                    font-size: 2.5mm;
                    text-align: left;
                    word-wrap: break-word;
                    line-height: 1.1;
                }
                .divider {
                    width: 100%;
                    border-bottom: 0.3mm solid black;
                    margin-top: 50mm;
                }
                /* Force spacing between labels */
                .label::after {
                    content: '';
                    display: block;
                    height: 50mm;
                    width: 100%;
                    position: absolute;
                    bottom: -50mm;
                    left: 0;
                }
            </style>
        </head>
        <body>
            <div class="label-container">
                ${labelItems.map((label, index) => `
                    <div class="label">
                        <div class="header">
                            <div class="ticket-number">#${label.ticket}</div>
                            <div class="count">${label.count}</div>
                        </div>
                        <div class="datetime">${formattedDate}<br>${formattedTime}</div>
                        <div class="item">${label.item}</div>
                    </div>
                    ${index === labelItems.length - 1 ? '<div class="divider"></div>' : ''}
                `).join('')}
            </div>
        </body>
        </html>
    `;

    // Write the content to the iframe and print it
    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write(content);
    doc.close();

    // Print immediately
    iframe.contentWindow.print();
    
    // Remove the iframe after printing
    setTimeout(() => {
        document.body.removeChild(iframe);
    }, 1000);
}
