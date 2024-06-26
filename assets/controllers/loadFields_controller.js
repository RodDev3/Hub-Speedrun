import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";


export default class extends Controller {
    connect() {

        let form = document.querySelector("#formRuns");
        let category = document.querySelector("#runs_refCategories");
        let loadFields = document.querySelector("#loadFields");


        //Evenement onload
        document.addEventListener("DOMContentLoaded", async function (e) {
            e.preventDefault();
            let formData = new FormData(form);

            loadFields.insertAdjacentHTML("beforeend", "<div id=\"loader\" class=\"mt-4 w-100 d-flex justify-content-center\"> <div class=\"spinner-border text-white \" role=\"status\">" +
                "<span class=\"visually-hidden\">Loading...</span>" +
                "</div></div>");

            let response = await fetch("/runs/fields/call", {
                method: "POST",
                body: formData
            });

            let data = await response.json();
            if (response.status === 200) {

                let loader = document.querySelector("#loader");
                loader.remove();

                loadFields.insertAdjacentHTML("beforeend", data);
            } else if (response.status === 400) {
                toastr.error(data.message, "Error");
            }
        });

        //Evenement onchange
        category.addEventListener("change", async function (e) {

            //TODO découper la page en grande catégorie comme Players / Times / Video / Others"
            loadFields.innerHTML = "";
            e.preventDefault();
            let formData = new FormData(form);

            loadFields.insertAdjacentHTML("beforeend", "<div id=\"loader\" class=\"mt-4 w-100 d-flex justify-content-center\"> <div class=\"spinner-border text-white \" role=\"status\">" +
                "<span class=\"visually-hidden\">Loading...</span>" +
                "</div></div>");

            let response = await fetch("/runs/fields/call", {
                method: "POST",
                body: formData
            });

            let data = await response.json();
            if (response.status === 200) {

                let loader = document.querySelector("#loader");
                loader.remove();

                loadFields.insertAdjacentHTML("beforeend", data);
            } else if (response.status === 400) {
                toastr.error(data.message, "Error");
            }

            //Switch sur ce que je renvoie pour les afficher, affiché en 1er le primary et si on secondary existe l'affiche ensuite et les autres pour les goals


        });
    }
}
