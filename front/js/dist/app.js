"use strict";
const numeroExpediteur = document.querySelector("#numeroExpediteur");
const montant = document.querySelector("#montant");
const fournisseur = document.querySelector("#fournisseur");
const transaction = document.querySelector("#transaction");
const infoExpediteur = document.querySelector("#infoExpediteur");
const numeroDestinaire = document.querySelector("#numeroDestinaire");
const infoDestinataire = document.querySelector("#infoDestinataire");
const valider = document.querySelector(".btn-success");
const destinataire = document.querySelector("#destinataire");
const messageSucces = document.querySelector("#succes");
const messageDanger = document.querySelector("#danger");
const typeTransaction = document.querySelector("#typeTransaction");
const modal = document.querySelector("#modal");
const infoModal = document.querySelector("#infoModal");
const ok = document.querySelector("#ok");
let port = "http://127.0.0.1:8000/api";
ok === null || ok === void 0 ? void 0 : ok.addEventListener("click", () => {
    modal.classList.remove("d-block");
    modal.classList.add("d-none");
});
fournisseur === null || fournisseur === void 0 ? void 0 : fournisseur.addEventListener("change", () => {
    if ((fournisseur === null || fournisseur === void 0 ? void 0 : fournisseur.value) == "orange money") {
        typeTransaction === null || typeTransaction === void 0 ? void 0 : typeTransaction.classList.remove("d-none");
        typeTransaction === null || typeTransaction === void 0 ? void 0 : typeTransaction.classList.add("d-block");
        typeTransaction.innerHTML = "";
        typeTransaction.innerHTML = `<option value="avec code">avec code</option>
        <option value="sans code">sans code</option> 
        `;
    }
    else if ((fournisseur === null || fournisseur === void 0 ? void 0 : fournisseur.value) == "compte bancaire") {
        typeTransaction === null || typeTransaction === void 0 ? void 0 : typeTransaction.classList.remove("d-none");
        typeTransaction === null || typeTransaction === void 0 ? void 0 : typeTransaction.classList.add("d-block");
        typeTransaction.innerHTML = "";
        typeTransaction.innerHTML = `<option value="permanent">permanent</option>
        <option value="immediat">immediat</option> 
        `;
    }
    else {
        typeTransaction === null || typeTransaction === void 0 ? void 0 : typeTransaction.classList.remove("d-block");
        typeTransaction === null || typeTransaction === void 0 ? void 0 : typeTransaction.classList.add("d-none");
    }
});
transaction === null || transaction === void 0 ? void 0 : transaction.addEventListener("change", () => {
    if (transaction.value == "retrait") {
        destinataire === null || destinataire === void 0 ? void 0 : destinataire.classList.remove("d-flex");
        destinataire === null || destinataire === void 0 ? void 0 : destinataire.classList.add("d-none");
    }
    else {
        destinataire === null || destinataire === void 0 ? void 0 : destinataire.classList.add("d-flex");
        destinataire === null || destinataire === void 0 ? void 0 : destinataire.classList.remove("d-none");
    }
    console.log();
});
valider === null || valider === void 0 ? void 0 : valider.addEventListener("click", () => {
    if (transaction.value == "transfert") {
        let objet = {
            "destinataire": numeroDestinaire === null || numeroDestinaire === void 0 ? void 0 : numeroDestinaire.value,
            "montant": montant === null || montant === void 0 ? void 0 : montant.value,
            "expediteur": numeroExpediteur === null || numeroExpediteur === void 0 ? void 0 : numeroExpediteur.value,
            "fournisseur": fournisseur === null || fournisseur === void 0 ? void 0 : fournisseur.value,
            "typeTransaction": typeTransaction.value
        };
        fetchPost(port + "/transfert", objet)
            .then(data => {
            console.log(data.statut);
            if ((data === null || data === void 0 ? void 0 : data.statut) == 200) {
                messageSucces === null || messageSucces === void 0 ? void 0 : messageSucces.classList.remove("d-none");
                messageSucces.textContent = data.message;
                messageSucces === null || messageSucces === void 0 ? void 0 : messageSucces.classList.add("d-block");
                setTimeout(() => {
                    messageSucces === null || messageSucces === void 0 ? void 0 : messageSucces.classList.add("d-none");
                    messageSucces === null || messageSucces === void 0 ? void 0 : messageSucces.classList.remove("d-block");
                }, 2000);
                if (typeTransaction.value == "avec code" || typeTransaction.value == "immediat") {
                    modal === null || modal === void 0 ? void 0 : modal.classList.remove("d-none");
                    modal === null || modal === void 0 ? void 0 : modal.classList.add("d-flex");
                    infoModal.innerHTML = "";
                    infoModal.innerHTML = `
                    <h4>transfert :<span>${typeTransaction.value}</span></h4>
                    <h5>expediteur <span>${numeroExpediteur === null || numeroExpediteur === void 0 ? void 0 : numeroExpediteur.value}</span></h5>
                    <h5>destinataire <span>${numeroDestinaire === null || numeroDestinaire === void 0 ? void 0 : numeroDestinaire.value}</span></h5>
                    <h5>code : <span>${data.code}</span></h5>
                                    
                    `;
                }
            }
            else {
                messageDanger.textContent = data.message;
                messageDanger === null || messageDanger === void 0 ? void 0 : messageDanger.classList.remove("d-none");
                messageDanger === null || messageDanger === void 0 ? void 0 : messageDanger.classList.add("d-block");
                setTimeout(() => {
                    messageDanger === null || messageDanger === void 0 ? void 0 : messageDanger.classList.add("d-none");
                    messageDanger === null || messageDanger === void 0 ? void 0 : messageDanger.classList.remove("d-block");
                }, 2000);
            }
            console.log(data);
        });
    }
    else {
        let objet = {
            "destinataire": numeroDestinaire === null || numeroDestinaire === void 0 ? void 0 : numeroDestinaire.value,
            "montant": montant === null || montant === void 0 ? void 0 : montant.value,
            "type_transaction": transaction === null || transaction === void 0 ? void 0 : transaction.value,
            "expediteur": numeroExpediteur === null || numeroExpediteur === void 0 ? void 0 : numeroExpediteur.value,
            "fournisseur": fournisseur === null || fournisseur === void 0 ? void 0 : fournisseur.value
        };
        fetchPost(port + "/transactions", objet)
            .then(data => {
            console.log(data.statut);
            if ((data === null || data === void 0 ? void 0 : data.statut) == 200) {
                messageSucces === null || messageSucces === void 0 ? void 0 : messageSucces.classList.remove("d-none");
                messageSucces.textContent = data.message;
                messageSucces === null || messageSucces === void 0 ? void 0 : messageSucces.classList.add("d-block");
                setTimeout(() => {
                    messageSucces === null || messageSucces === void 0 ? void 0 : messageSucces.classList.add("d-none");
                    messageSucces === null || messageSucces === void 0 ? void 0 : messageSucces.classList.remove("d-block");
                }, 2000);
            }
            else {
                messageDanger.textContent = data.message;
                messageDanger === null || messageDanger === void 0 ? void 0 : messageDanger.classList.remove("d-none");
                messageDanger === null || messageDanger === void 0 ? void 0 : messageDanger.classList.add("d-block");
                setTimeout(() => {
                    messageDanger === null || messageDanger === void 0 ? void 0 : messageDanger.classList.add("d-none");
                    messageDanger === null || messageDanger === void 0 ? void 0 : messageDanger.classList.remove("d-block");
                }, 2000);
            }
            console.log(data);
        });
    }
});
function fetchPost(url, objet) {
    return fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(objet)
    })
        .then(response => response.json())
        .then(data => data);
}
