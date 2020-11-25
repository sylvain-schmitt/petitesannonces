window.onload = () => {
    //Gestion des switch pour activer/désactiver "annonces"
    let activer = document.querySelectorAll('.custom-control-input')
    for (let bouton of activer) {
        bouton.addEventListener("click", function () {
            let xmlhttp = new XMLHttpRequest;
            xmlhttp.open("get", `/admin/annonces/activer/${this.dataset.id}`)
            xmlhttp.send()
        })
    }

    //Gestion des Modal pour supprimer une "annonces" Admin
    let supprimer = document.querySelectorAll('.modal-trigger')
    for (let bouton of supprimer) {
        bouton.addEventListener("click", function () {
            document.querySelector(".modal-footer a").href = `/admin/annonces/supprimer/${this.dataset.id}`
            document.querySelector(".modal-body").innerText = `Êtes-vous sûr(e) de vouloir supprimer l'annonce "${this.dataset.titre}"`
        })

    }

    // Gestion des boutons "Supprimer"
    let links = document.querySelectorAll("[data-delete]")

    // On boucle sur links
    for (link of links) {
        // On écoute le clic
        link.addEventListener("click", function (e) {
            // On empêche la navigation
            e.preventDefault()

            // On demande confirmation
            if (confirm("Voulez-vous supprimer cette image ?")) {
                // On envoie une requête Ajax vers le href du lien avec la méthode DELETE
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ "_token": this.dataset.token })
                }).then(
                    // On récupère la réponse en JSON
                    response => response.json()
                ).then(data => {
                    if (data.success)
                        this.parentElement.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }	
    
       // Carousel Bootstrap
    $('#myCarousel').on('slide.bs.carousel', function () {
        $('.carousel').carousel({
            interval: 1000
        })
    })
    
     //Gestion des Modal pour supprimer une "annonces" utilisateur
 let suppr = document.querySelectorAll('.modal-trig')
 for (let bouton of suppr) {
     bouton.addEventListener("click", function () {
         document.querySelector(".modal-footer a").href = `/users/annonces/supprimer/${this.dataset.id}`
         document.querySelector(".modal-body").innerText = `Êtes-vous sûr(e) de vouloir supprimer l'annonce "${this.dataset.titre}"`
     })

 }
    
}