import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";
import {isGeneratorFunction} from "regenerator-runtime";


export default class extends Controller {
    connect() {

        this.leaderboard = document.querySelector("#leaderboard");
        this.categories = this.element.querySelectorAll(".categories");
        this.gameRewrite = window.location.href.split("/").slice(-1);

        if (this.categories.length !== 0) {
            window.addEventListener("load", (e) => {
                    e.preventDefault();
                    this.applyCategories(e, this.categories[0]);
                }
            );
        }

        if(this.categories.length === 0){
            this.leaderboard.innerHTML = '<div class="alert alert-primary" role="alert">\n' +
                '  Leaderboards in construction\n' +
                '</div>';
        }
        this.categories.forEach((category) => {
            category.addEventListener("click", (e) => {
                this.applyCategories(e, category);
            });
        });

    }

    async applyCategories(e, category) {
        e.preventDefault();

        let oldActive = document.querySelector('.categories.active');
        if (oldActive !== null){
            oldActive.classList.remove('active');
        }
        category.classList.add('active');

        this.leaderboard.innerHTML = '<div id="loader" class="mt-4"> ' +
            '<div class="spinner-border text-white" role="status">' +
            '<span class="visually-hidden">Loading...</span>' +
            "</div></div>";

        let response = await fetch("/game/call/categories", {
            method: "POST",
            body: JSON.stringify({
                "categories": category.getAttribute("data-categories"),
                "games": this.gameRewrite[0]
            })
        });

        let data = await response.json();
        if (response.status === 200) {

            this.leaderboard.innerHTML = data;

            let categories = document.querySelector("#subCategoriesWrapper");
            if (categories) {
                await this.loadRuns(e, category);
            }

        } else {
            toastr.error(data.message, "Error");
        }

    }

    async loadRuns(e, category) {
        e.preventDefault();

        let dataForm = null;
        let value = null;


        if (category === undefined) {
            //From subCategories button

            //loader
            let divRuns = document.querySelector("#runs");
            divRuns.innerHTML = "<div id=\"loader\" class=\"mt-4 d-flex justify-content-center\"> <div class=\"spinner-border text-white\" role=\"status\">" +
                "<span class=\"visually-hidden\">Loading...</span>" +
                "</div></div>";

            //Build du json pour l'envoie
            dataForm = [];
            let subCategories = document.querySelectorAll(".subCategories");
            dataForm = {"uuid": subCategories[0].getAttribute("data-item-category-param"), "subCategory": {}};

            subCategories.forEach((subCategory, index) => {
                let key = subCategory.getAttribute("data-item-label-param");
                dataForm.subCategory[key] = subCategory.value;

            });

        } else {
            //From categories Button

            this.leaderboard.insertAdjacentHTML("beforeend", "<div id=\"loader\" class=\"mt-4 d-flex justify-content-center\"> <div class=\"spinner-border text-white\" role=\"status\">" +
                "<span class=\"visually-hidden\">Loading...</span>" +
                "</div></div>");

            dataForm = {"uuid": category.getAttribute("data-categories"), "subCategory": null};
        }


        let response = await fetch("/categories/call/subCategories/runs", {
            method: "POST",
            body: JSON.stringify(dataForm)
        });

        let data = await response.json();
        if (response.status === 200) {

            if (category === undefined) {
                let divRuns = document.querySelector("#runs");
                document.querySelector("#loader").remove();

                divRuns.innerHTML = data;
            } else {
                document.querySelector("#loader").remove();
                this.leaderboard.insertAdjacentHTML("beforeend", data);
            }

        } else {
            toastr.error(data.message, "Error");
        }
    }

}
