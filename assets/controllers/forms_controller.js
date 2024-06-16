import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";


export default class extends Controller {
    static values = {
        uuid: String,
        id: String,
        username: String
    };

    async newUserForm(event){
        event.preventDefault();

        let form = event.target;
        let response = await fetch('/users/new/call', {
            method : "POST",
            body : new FormData(form)
        })


        let data = await response.json();
        if (response.status === 200){
            toastr.success(data.message, 'Success');
            setTimeout( () => {
                window.location.href = data.redirect
            }, 1500)
        }else{
            toastr.error(data.message, "Error")
        }

    }

    async gameForm(event){
        event.preventDefault();

        if (this.uuidValue === "") {
            this.uuidValue = null;
        }


        let form = event.target;
        let response = await fetch('/call/form/'+this.uuidValue, {
            method : "POST",
            body : new FormData(form)
        })


        let data = await response.json();
        if (response.status === 200){
            toastr.success(data.message, 'Success');
            setTimeout( () => {
                window.location.href = data.redirect
            }, 1500)
        }else{
            toastr.error(data.message, "Error")
        }
    }

    async supportForm(event) {
        event.preventDefault();

        if (this.idValue === "") {
            this.idValue = null;
        }


        let form = event.target;
        let response = await fetch('/supports/call/' + this.idValue, {
            method : "POST",
            body : new FormData(form)
        })


        let data = await response.json();
        if (response.status === 200){
            toastr.success(data.message, 'Success');
            setTimeout( () => {
                window.location.href = data.redirect
            }, 1500)
        }else{
            toastr.error(data.message, "Error")
        }
    }

    async updateUser(event) {
        event.preventDefault();

        console.log(this.usernameValue);

        let form = event.target;
        let response = await fetch('/users/update/call/' + this.usernameValue, {
            method : "POST",
            body : new FormData(form)
        })


        let data = await response.json();
        if (response.status === 200){
            toastr.success(data.message, 'Success');
            setTimeout( () => {
                window.location.href = data.redirect
            }, 1500)
        }else{
            toastr.error(data.message, "Error")
        }
    }
}
