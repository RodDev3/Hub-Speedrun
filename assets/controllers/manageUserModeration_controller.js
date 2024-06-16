import { Controller } from '@hotwired/stimulus';
import TomSelect from "tom-select";
import toastr from "toastr";


export default class extends Controller {

    ajaxUrl = "call/user";
    static values = {
        rewrite: String
    }


    connect() {
        this.form = this.element.querySelector('#manageUsersModeration');
        let userSelect = this.element.querySelector('#moderations_refUsers');

        new TomSelect('#moderations_refUsers', {});


        window.addEventListener('load',(e) => {
            this.fetchData(new FormData(this.form));
        });

        userSelect.tomselect.on('change', (e) => {
           this.fetchData(new FormData(this.form));
        });
    }




    async fetchData(data) {

        console.log(this.ajaxUrl);
        let ajax = await fetch(this.ajaxUrl, {
            body: data,
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

        let rolesDiv = document.querySelector('#roles')
        rolesDiv.innerHTML = json['twigTemplate']



    }

    async applyChanges(event){
        event.preventDefault();

        let ajax = await fetch('call/moderation/submit', {
            body: new FormData(this.form),
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
        toastr.success(json.message, 'Success')
    }

    async delete(event){
        event.preventDefault();

        let ajax = await fetch('call/moderation/delete', {
            body: new FormData(this.form),
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
        toastr.success(json.message, 'Success')
    }
}