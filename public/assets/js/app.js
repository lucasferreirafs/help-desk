document.addEventListener("DOMContentLoaded", () => {


    document.querySelectorAll("[data-password-toggle]").forEach((button) => {
        button.addEventListener("click", () => {
            const input = button.parentElement?.querySelector("input");
            if (!input) return;
            input.type = input.type === "password" ? "text" : "password";

            const icon = button.querySelector("i");
            if (icon) {
                icon.classList.toggle("fa-eye");
                icon.classList.toggle("fa-eye-slash");
            }

            button.setAttribute(
                "aria-label",
                input.type === "password" ? "Mostrar senha" : "Ocultar senha",
            );
        })
    });
});

