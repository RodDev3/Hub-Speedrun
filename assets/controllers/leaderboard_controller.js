import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";


export default class extends Controller {
    connect() {

        this.leaderboard = document.querySelector("#leaderboard");
        this.categories = this.element.querySelectorAll(".categories");
        this.gameRewrite = window.location.href.split("/").slice(-1);

        this.categories.forEach((category) => {
            category.addEventListener("click", (e) => {
                this.applyCategories(e, category);
            });
        });

    }

    async applyCategories(e, category) {
        e.preventDefault();

        let response = await fetch("/game/call/categories", {
            method: "POST",
            body: JSON.stringify({
                "categories": category.getAttribute("data-categories"),
                "games": this.gameRewrite[0]
            })
        });

        let data = await response.json();
        if (response.status === 200) {
            console.log(data);
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

            //Build du json pour l'envoie
            dataForm = [];
            let subCategories = document.querySelectorAll('.subCategories');
            dataForm = {'uuid' : subCategories[0].getAttribute('data-item-category-param'), 'subCategory' : {}};

            subCategories.forEach((subCategory, index) => {
                let key = subCategory.getAttribute('data-item-label-param');
                dataForm.subCategory[key] = subCategory.value;

            });

        } else {
            //From categories Button
            dataForm = {'uuid' : category.getAttribute("data-categories"), 'subCategory' : null};
        }


        let response = await fetch("/categories/call/subCategories/runs", {
            method: "POST",
            body: JSON.stringify(dataForm)
        });

        let data = await response.json();
        if (response.status === 200) {
            //TODO Add loader

            if (category === undefined) {
                let divRuns = document.querySelector('#runs');
                divRuns.innerHTML = data;
            }else{
                this.leaderboard.insertAdjacentHTML('beforeend', data);
            }

        } else {
            toastr.error(data.message, "Error");
        }
    }

}
