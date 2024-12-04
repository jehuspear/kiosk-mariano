// Simulate charts using Chart.js
document.addEventListener("DOMContentLoaded", () => {
    const ctxCustomer = document.getElementById("customerChart").getContext("2d");
    const ctxSelling = document.getElementById("bestSellingChart").getContext("2d");
  
    // Customer Count Chart
    new Chart(ctxCustomer, {
      type: "line",
      data: {
        labels: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
        datasets: [
          {
            label: "Customer Count",
            data: [5, 7, 12, 10, 8, 5, 6],
            borderColor: "#00ffff",
            backgroundColor: "rgba(0, 255, 255, 0.1)",
          },
        ],
      },
      options: {
        responsive: true,
      },
    });
  
    // Best Selling Chart
    new Chart(ctxSelling, {
      type: "bar",
      data: {
        labels: ["Salted Caramel", "Cafe Mocha", "Spanish Latte", "Cafe Latte", "Americano", "Cappuccino", "Honey Cinnamon"],
        datasets: [
          {
            label: "Sales",
            data: [20, 15, 30, 25, 10, 15, 5],
            backgroundColor: "#00ffff",
          },
        ],
      },
      options: {
        responsive: true,
      },
    });
  });
  