import { usePage } from "@inertiajs/vue3";
import { ref } from "vue";

export default async (props, detalhesOffcanvas) => {
    const appUrl = usePage().props.app_url;
    const { lat, lng } = props.latLng;
    const popupContent = document.createElement("div");
    let servicos = ref([]);

    await Promise.all([
        axios.get(
            route("contratos.gestao.get-servicos", {
                contrato: props.id,
            })
        ),
    ])
        .then((r) => {
            servicos.value = r[0].data;
        })
        .catch((e) => {
            console.log(e);
        });

    let html = `
        <h5>Licença</h5>
        <hr class="my-2" />
        <strong>ID: </strong><a href="${route("dashboard.pmqa", props.id)}">${
        props.id
    }</a><br>
        <strong>Unidade Gestora: </strong>${props.unidade_gestora}<br>
        <strong>N° do Contrato: </strong>${props.numero_contrato}<br>
        <strong>Contratada: </strong>${props.contratada}<br>
        <strong>Edital: </strong>${props.edital}<br>
        <strong>UF/BR: </strong>${props.uf}/${props.rodovia}<br>
        <strong>Situação: </strong>${props.situacao}<br>
    `;

    const btnDetail = document.createElement("button");
    btnDetail.classList.add(
        "btn",
        "btn-dark",
        "px-2",
        "py-1",
        "flex-fill",
        "btn-popup-detalhes"
    );
    btnDetail.dataset.bsToggle = "offcanvas";
    btnDetail.dataset.bsTarget = "#offcanvasDetalhes";
    btnDetail.innerText = "Ver Detalhes";
    btnDetail.onclick = () => {
        detalhesOffcanvas.value.propsData = props;
        detalhesOffcanvas.value.servicos = servicos.value;
    };

    html += `
        <hr class="my-2"/>
        <div class="d-flex gap-2 action-buttons">
        </div>
        <hr class="my-2"/>
        <div class='d-flex justify-content-between align-items-center'>
            <a href="https://www.google.com/maps?layer=c&cbll=${lat},${lng}" title="Navegar pelas imagens do Street View" target="_blank">
                <img width="25" class="icon-steeet-view" src='${appUrl}/img/street-view.png' alt="Street View" /> Street View
            </a>
        </div>
    `;

    popupContent.innerHTML = html;
    popupContent.querySelector(".action-buttons").prepend(btnDetail);

    return popupContent;
};
