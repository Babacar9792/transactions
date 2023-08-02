// console.log("bonjour");
// alert("bonjour");



const numeroExpediteur = document.querySelector("#numeroExpediteur") as HTMLInputElement;
const montant = document.querySelector("#montant") as HTMLInputElement;
const fournisseur = document.querySelector("#fournisseur") as HTMLSelectElement;
const transaction = document.querySelector("#transaction") as HTMLSelectElement;
const infoExpediteur = document.querySelector("#infoExpediteur") as HTMLInputElement;
const numeroDestinaire = document.querySelector("#numeroDestinaire") as HTMLInputElement;
const infoDestinataire = document.querySelector("#infoDestinataire") as HTMLInputElement;
const valider = document.querySelector(".btn-success");
const destinataire = document.querySelector("#destinataire") as HTMLInputElement;
const messageSucces = document.querySelector("#succes")!;
const messageDanger = document.querySelector("#danger")!;
const typeTransaction = document.querySelector("#typeTransaction") as  HTMLSelectElement;
const modal = document.querySelector("#modal")!;
const infoModal = document.querySelector("#infoModal")!;
const ok = document.querySelector("#ok");
let port = "http://127.0.0.1:8000/api"


ok?.addEventListener("click", ()=>{
    modal.classList.remove("d-block");
    modal.classList.add("d-none");
})


fournisseur?.addEventListener("change", ()=>{

    if(fournisseur?.value == "orange money")
    {
        typeTransaction?.classList.remove("d-none");
        typeTransaction?.classList.add("d-block");
        typeTransaction.innerHTML = "";
        typeTransaction.innerHTML = `<option value="avec code">avec code</option>
        <option value="sans code">sans code</option> 
        `;
    }
    else if(fournisseur?.value == "compte bancaire")
    {
        typeTransaction?.classList.remove("d-none");
        typeTransaction?.classList.add("d-block");
        typeTransaction.innerHTML = "";
        typeTransaction.innerHTML = `<option value="permanent">permanent</option>
        <option value="immediat">immediat</option> 
        `;

    }
    else{
        typeTransaction?.classList.remove("d-block");
        typeTransaction?.classList.add("d-none");
    }
})


transaction?.addEventListener("change", ()=>
{
    if(transaction.value == "retrait")
    {
        destinataire?.classList.remove("d-flex");
        destinataire?.classList.add("d-none");

    }
    else
    {
        destinataire?.classList.add("d-flex");
        destinataire?.classList.remove("d-none");
    }
    console.log();
})


valider?.addEventListener("click", ()=>{
    // console.log("bonjour");
    if(transaction.value == "transfert")
    {
        let objet = {
            "destinataire" : numeroDestinaire?.value,
            "montant" : montant?.value,
            // "type_transaction" : transaction?.value,
            "expediteur" : numeroExpediteur?.value,
            "fournisseur" : fournisseur?.value,
            "typeTransaction" : typeTransaction.value
        }
        fetchPost(port+"/transfert", objet)
    
        .then(data => {
            console.log(data.statut )
            if(data?.statut == 200)
            {

                messageSucces?.classList.remove("d-none");
                messageSucces.textContent = data.message;
                messageSucces?.classList.add("d-block");

                setTimeout(() => {
                    
                    messageSucces?.classList.add("d-none");
                    messageSucces?.classList.remove("d-block");
                }, 2000);
                if(typeTransaction.value == "avec code" || typeTransaction.value == "immediat")
                {
                    modal?.classList.remove("d-none");
                    modal?.classList.add("d-flex");

                    infoModal.innerHTML = "";
                    infoModal.innerHTML = `
                    <h4>transfert :<span>${typeTransaction.value}</span></h4>
                    <h5>expediteur <span>${numeroExpediteur?.value}</span></h5>
                    <h5>destinataire <span>${numeroDestinaire?.value}</span></h5>
                    <h5>code : <span>${data.code}</span></h5>
                                    
                    `;

                }

            }
            else 
            {
                messageDanger.textContent = data.message;
                messageDanger?.classList.remove("d-none");
                messageDanger?.classList.add("d-block");

                setTimeout(() => {
                    
                    messageDanger?.classList.add("d-none");
                    messageDanger?.classList.remove("d-block");
                }, 2000);


            }
            console.log(data)
        })
    }
    else 
    {

    let objet = {
        "destinataire" : numeroDestinaire?.value,
        "montant" : montant?.value,
        "type_transaction" : transaction?.value,
        "expediteur" : numeroExpediteur?.value,
        "fournisseur" : fournisseur?.value
    }
    fetchPost(port+"/transactions", objet)
    
        .then(data => {
            console.log(data.statut )
            if(data?.statut == 200)
            {
                messageSucces?.classList.remove("d-none");
                messageSucces.textContent = data.message;
                messageSucces?.classList.add("d-block");

                setTimeout(() => {
                    
                    messageSucces?.classList.add("d-none");
                    messageSucces?.classList.remove("d-block");
                }, 2000);

            }
            else 
            {
                messageDanger.textContent = data.message;
                messageDanger?.classList.remove("d-none");
                messageDanger?.classList.add("d-block");

                setTimeout(() => {
                    
                    messageDanger?.classList.add("d-none");
                    messageDanger?.classList.remove("d-block");
                }, 2000);


            }
            console.log(data)
        })
 
    }   
    // console.log(objet);
})



function fetchPost(url : string, objet :{}) {
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


