document.addEventListener("DOMContentLoaded", () => {
    
    const titleModal = document.getElementById('confirmModalLabel');
    const bodyModal = document.getElementById('confirmModalBody');
    const confirmModalMethod = document.getElementById('confirmModalMethod');
    const modalForm = document.getElementById('confirmModalForm');
    
    
    
    const modal = document.getElementById('confirmModal');
    modal.addEventListener('show.bs.modal', function (e) {
        const triggerButton = e.relatedTarget;
        const link = triggerButton.getAttribute('data-url');
        const title = triggerButton.getAttribute('data-title') || 'Action à confirmer';
        const body = triggerButton.getAttribute('data-body') || 'Êtes-vous sûr de vouloir continuer cette action ?';
        switch (triggerButton.getAttribute('data-method')) {
            case 'PUT':
            case 'DELETE':
                case 'PATCH':
                confirmModalMethod.setAttribute('value', triggerButton.getAttribute('data-method'));
                {
                    const methodValue = triggerButton.getAttribute('data-method');
                    let methodInput = document.getElementById('confirmModalMethod');

                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.id = 'confirmModalMethod';
                        methodInput.name = '_method';
                        modalForm.appendChild(methodInput);
                    }
                    
                    methodInput.value = methodValue;
                    modalForm.setAttribute('method', 'POST');
                }
                break;
                case 'POST':
                    modalForm.setAttribute('method', 'POST');
                    confirmModalMethod.remove();
                    break;
                    default:
                        modalForm.setAttribute('method', 'GET');
                        confirmModalMethod.remove();
                    }
                    titleModal.textContent = title;
        bodyModal.textContent = body;
        


        modalForm.setAttribute('action', link);
    });
    
    const buttons = document.querySelectorAll(".load-btn");
    buttons.forEach((btn) => {
        btn.addEventListener("click", (e) => {
            const icon = btn.querySelector("i");
            const spinner = btn.querySelector(".spinner-border");
            const text = btn.querySelector(".btn-text");
            
            const loadingText = btn.dataset.loadingText || "Chargement...";
            
            if (btn.type === "submit") {
                const form = btn.closest("form");
                if (form && !form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    form.classList.add("was-validated");
                    return;
                }
            }
            
            if (icon) icon.classList.add("d-none");
            if (spinner) spinner.classList.remove("d-none");
            if (text) text.textContent = loadingText;
        });
    });
});
