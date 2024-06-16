import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";


export default class extends Controller {
    connect() {

    }

    async submitRun(e) {
        e.preventDefault();

        let formData = new FormData(this.element);

        let response = await fetch("/runs/submit/call", {
            method: "POST",
            body: formData
        });

        let data = await response.json();

        if (response.status === 200) {
            toastr.success(data.message, "Success");
            if (data.redirect !== undefined){
                setTimeout(() => {
                    window.location.href = data.redirect
                }, 1500)
            }
        } else if (response.status === 400) {
            toastr.error(data.message, "Error");
        }
    }

    async checkRun(e) {
        e.preventDefault();

        let response = await fetch("/runs/verification/call", {
            method: "POST",
            body: new FormData(this.element.querySelector("#checkRun"))
        });

        let data = await response.json();
        if (response.status !== 200) {
            toastr.error(data.message, "Error");
        }
        toastr.success(data.message, "Success");
    }

    async validateRun(e) {
        e.preventDefault();

        let response = await fetch("/runs/validation/call", {
            method: "POST",
            body: new FormData(this.element.querySelector("#checkRun"))
        });

        let data = await response.json();
        if (response.status !== 200) {
            toastr.error(data.message, "Error");
        } else {

            toastr.success(data.message, "Success");

            setTimeout(function () {
                if (data.url !== undefined) {
                    window.location.href = data.url;
                }
            }, 2000);
        }
    }

    async rejectRun(e) {
        e.preventDefault();

        let response = await fetch("/runs/reject/call", {
            method: "POST",
            body: new FormData(this.element.querySelector("#checkRun"))
        });

        let data = await response.json();
        if (response.status !== 200) {
            toastr.error(data.message, "Error");
        } else {
            toastr.success(data.message, "Success");
            setTimeout(function () {
                if (data.url !== undefined) {
                    window.location.href = data.url;
                }
            }, 2000);
        }
    }
}
