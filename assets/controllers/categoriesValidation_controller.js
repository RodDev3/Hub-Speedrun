import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";


export default class extends Controller {
    static values = {
        uuid: String
    };

    connect() {

        if (this.uuidValue === "") {
            this.uuidValue = null;
        }
        let form = document.querySelector("#formCategories");

        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            let formData = new FormData(form);

            try {
                let response = await fetch("/categories/call/submit/" + this.uuidValue, {
                    method: "POST",
                    body: formData
                });

                let data = await response.json();
                switch (response.status) {
                    case 200 :
                        toastr.success(data.message, "Category created");
                        if (data.redirect !== undefined) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1500);
                        }
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
