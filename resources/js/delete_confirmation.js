// Lista pulsanti di cancellazione
const deleteForms = document.querySelectorAll('.delete-form')
// Modale
const modal = document.getElementById('delete-modal');
const modalText = document.querySelector('.modal-body');
// Pulsante conferma nella modale
const confirmButton = document.querySelector('.confirm-btn');
// Pulsante chiusura nella modale
const closeButton = document.getElementById('button-close');

let activeForm = null;

deleteForms.forEach(form => {
    form.addEventListener('submit', e => {
        e.preventDefault();
        if (form.dataset.title) {
            modalText.innerText += ` ${form.dataset.title}?`;
        } else {
            modalText.innerText = "Tutti i progetti saranno cancellati definitivamente. Vuoi continuare?";
        }
        activeForm = form;

    })
})
confirmButton.addEventListener('click', () => {
    if (activeForm) activeForm.submit();
})
modal.addEventListener('hidden.bs.modal', () => {

    activeForm = null;
})
closeButton.addEventListener('click', () => {
    modal.style = "display: none"

})