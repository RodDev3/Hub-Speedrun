import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";


export default class extends Controller {
    connect() {

        /*let test1 = new FormData(this)*/
        let form = document.querySelector("#formCategories");
        //TODO DONC SUPPRIMER LA VERIF DANS app_categories_new

        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            let formData = new FormData(form);

            try {
                let response = await fetch("/categories/call/submit", {
                    method: "POST",
                    body: formData
                });

                let data = await response.json();
                switch (response.status) {
                    case 200 :
                        toastr.success(data.message, "Category created");
                        break;
                    default:
                        toastr.error(data.message, "Error");
                        break;
                }

            } catch (error) {
                //console.log(error)
                toastr.error("An error occurred", "Error");
            }

        });
    }
}
