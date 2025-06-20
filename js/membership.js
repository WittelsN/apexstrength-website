document.addEventListener("DOMContentLoaded", () => {
  const popup = document.getElementById("payment-popup");
  const paymentAmount = document.getElementById("payment-amount");
  const loading = document.getElementById("loading");
  const successMsg = document.getElementById("success-msg");
  let selectedPlan = "";
  
  document.querySelectorAll(".select-membership-btn").forEach(btn => {
    btn.addEventListener("click", function () {
      const card = btn.closest(".card");
      selectedPlan = card.dataset.plan;
      const price = card.dataset.price;
      paymentAmount.textContent = `Amount Due: R${price}`;
      popup.classList.add("active");
    });
  });

  document.getElementById("submit-payment-btn").addEventListener("click", () => {
    const bank = document.getElementById("bank-name").value;
    const card = document.getElementById("card-number").value;
    const cv = document.getElementById("cv").value;
    const expiry = document.getElementById("expiry-date").value;

    if (!bank || !card || !cv || !expiry) {
      alert("Please fill in all payment fields.");
      return;
    }

    loading.classList.remove("hidden");
    setTimeout(() => {
      loading.classList.add("hidden");
      successMsg.classList.remove("hidden");

      fetch("php/update_membership.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `membership=${selectedPlan}`
      });

      document.querySelectorAll(".select-membership-btn").forEach(btn => {
        btn.innerText = "Select";
      });

      document.querySelector(`.card[data-plan='${selectedPlan}'] .select-membership-btn`).innerHTML = "✔️";
    }, 3000);
  });
});

function closePopup() {
  document.getElementById("payment-popup").classList.remove("active");
  document.getElementById("success-msg").classList.add("hidden");
}
