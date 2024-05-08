import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";


export default class extends Controller {
    connect() {
        let form = document.querySelector("#formRuns");

        form.addEventListener('submit', async function (e){
            e.preventDefault();

            let formData = new FormData(form);

            let response = await fetch('/runs/submit/call', {
                method: 'POST',
                body: formData
            })

            let data = await response.json();

            if (response.status === 200){
                toastr.success(data.message, 'Success')
            }else if(response.status === 400){
                toastr.error(data.message, 'Error')
            }

        })
    }
}
