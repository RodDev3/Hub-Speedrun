import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";


export default class extends Controller {


    async newUserForm(event){
        event.preventDefault();

        let form = event.target;
        let response = await fetch('/users/new/call', {
            method : "POST",
            body : new FormData(form)
        })


        let data = await response.json();
        if (response.status === 200){

        }else{
            toastr.error(data.message, "Error")
        }

    }
}
