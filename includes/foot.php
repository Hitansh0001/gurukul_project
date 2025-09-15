</div>
</body>
<style>
    #customErrorModal {
        position: fixed;
        top: 5%;
        left: 50%;
        transform: translateX(-50%);
        background-color: white;
        border: 2px solid red;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        z-index: 1100;
        padding: 20px;
        max-width: 400px;
        width: 90%;
        font-family: Arial, sans-serif;
    }

    #customErrorModal h3 {
        margin-top: 0;
        color: red;
        font-size: 20px;
    }

    #customErrorModal p {
        color: #333;
        margin: 10px 0;
    }

    #customErrorModal button {
        margin-top: 15px;
        padding: 6px 12px;
        background-color: red;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
</style>

<script>
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.transition = "opacity 0.6s ease-out";
            alert.style.opacity = "0";

            setTimeout(() => {
                alert.style.display = "none";
            }, 6000);
        });
    }, 1500);


    function showAlertDiv(message) {
        // Remove existing modal if any
        const existing = document.getElementById("customErrorModal");
        if (existing) existing.remove();



        // Create modal container
        const modal = document.createElement("div");
        modal.id = "customErrorModal";

        // Add content
        modal.innerHTML = `
    <h3>Error</h3>
    <p>${message}</p>
    <button onclick="document.getElementById('customErrorModal').remove()">Close</button>
  `;

        // Append to body
        document.body.appendChild(modal);
    }

    function showAlert(type, message, duration = 3000) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-' + type;
        alert.innerHTML = `<strong>${message}</strong>`;

        document.body.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, duration);
    }
</script>

</html>