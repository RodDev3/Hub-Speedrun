import { Controller } from '@hotwired/stimulus';
import TomSelect from "tom-select";
import toastr from "toastr";


export default class extends Controller {

    ajaxUrl = "/users/call/admins/";


    connect() {}



    async add(event) {
        event.preventDefault()

        let form = document.querySelector('#manageAdminForm');

        let ajax = await fetch(this.ajaxUrl+'add', {
            body: new FormData(form),
            method: "POST"
        })

        let json;
        try {
            json = await ajax.json();
        } catch (e) {
            alert("Les données récupérées sont éronnées")
            return;
        }

        if (ajax.status !== 200) {
            toastr.error(json.message, 'Error');
            return;
        }

        toastr.success(json.message, 'Success');
        if (json.redirect !== undefined){
            setTimeout(()=>{
                window.location.href = json.redirect
            },1500)
        }

    }

    async delete(event){
        event.preventDefault();

        let form = document.querySelector('#manageAdminForm');

        let ajax = await fetch(this.ajaxUrl+'delete', {
            body: new FormData(form),
            method: "POST"
        })

        let json;
        try {
            json = await ajax.json();
        } catch (e) {
            alert("Les données récupérées sont éronnées")
            return;
        }

        if (ajax.status !== 200) {
            toastr.error(json.message, 'Error');
            return;

        }
        toastr.success(json.message, 'Success');
        if (json.redirect !== undefined){
            setTimeout(()=>{
                window.location.href = json.redirect
            },1500)
        }
    }
}