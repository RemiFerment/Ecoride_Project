document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".load-btn");

    buttons.forEach((btn) => {
        btn.addEventListener("click", (e) => {
            const icon = btn.querySelector("i");
            const spinner = btn.querySelector(".spinner-border");
            const text = btn.querySelector(".btn-text");

            const loadingText = btn.dataset.loadingText || "Chargement...";

            // Vérifie si c'est un bouton submit
            if (btn.type === "submit") {
                const form = btn.closest("form"); // récupère le form parent
                if (form && !form.checkValidity()) {
                    // Form invalide → on stoppe le submit
                    e.preventDefault();
                    e.stopPropagation();
                    form.classList.add("was-validated"); // si tu utilises Bootstrap validation
                    return; // on ne lance pas le spinner
                }
            }

            // Si on arrive ici → soit c'est pas un submit, soit form valide
            if (icon) icon.classList.add("d-none");
            if (spinner) spinner.classList.remove("d-none");
            if (text) text.textContent = loadingText;
        });
    });
});
