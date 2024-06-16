import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    connect() {
        let rows = document.querySelectorAll(".rowLeaderboard");
        rows.forEach((row) => {
            row.addEventListener("click", function (e) {
                e.preventDefault();
                window.location.href = row.getAttribute("data-href");
            });
        });
    }

}
