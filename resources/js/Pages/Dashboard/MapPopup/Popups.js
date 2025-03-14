import EcosistemaContratoPopup from "./EcosistemaContratoPopup";

// Listagem de layers por tipo/nome. Variáveis serão usadas para definir qual é a função responsável por retornar o conteúdo

const layersEcosistema = ["ecosistema_contrato_view"];

const getContent = async (groupName, props, detalhesOffcanvas = null) => {
    if (layersEcosistema.includes(groupName))
        return EcosistemaContratoPopup(props, detalhesOffcanvas);

    return "N/A";
};

export default {
    EcosistemaContratoPopup,
    getContent,
};
