<script setup>
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";

const props = defineProps({
    relatorio: {type: Object},
    servico: {type: Object}
});
</script>
<template>
    <div class="d-flex flex-column justify-content-between min-vh-100">
        <div class="mb-4">
            <h4>9. Tabela com a relação de ABIO's emitidas para o empreendimento</h4>
            <div class="card">
                <div class="table-responsive">
                    <table class="table card-table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">N° licença</th>
                            <th class="text-center">Empreendimento</th>
                            <th class="text-center">Emissor</th>
                            <th class="text-center">Data emissão</th>
                            <th class="text-center">Vencimento</th>
                            <th class="text-center">Responsável</th>
                            <th class="text-center">Precesso DNIT</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="abio in relatorio.resultado?.campanhas.map(campanha => campanha.abios).flat()"
                            :key="abio.id">
                            <td class="text-center">{{ abio.abio?.licenca?.tipo_rel?.sigla }}</td>
                            <td class="text-center">{{ abio.abio?.licenca?.numero_licenca }}</td>
                            <td class="text-center">{{ abio.abio?.licenca?.empreendimento }}</td>
                            <td class="text-center">{{ abio.abio?.licenca?.emissor }}</td>
                            <td class="text-center">{{ dateTimeFormat(abio.abio?.licenca?.data_emissao) }}</td>
                            <td class="text-center">{{ dateTimeFormat(abio.abio?.licenca?.vencimento) }}</td>
                            <td class="text-center">{{ abio.abio?.licenca?.fiscal }}</td>
                            <td class="text-center">{{ abio.abio?.licenca?.processo_dnit }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <h4>10. Dados das passagens de fauna</h4>
            <div class="card">
                <div class="table-responsive">
                    <table class="table card-table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">ID passagem</th>
                            <th class="text-center">Rodovia</th>
                            <th class="text-center">KM</th>
                            <th class="text-center">UF</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Classificação</th>
                            <th class="text-center">Dimensões</th>
                            <th class="text-center">Nome</th>
                            <th class="text-center">Data cadastro</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="passagem in servico.passagem_fauna_passagens"
                            :key="passagem.id">
                            <td class="text-center">{{ passagem.id }}</td>
                            <td class="text-center">{{ passagem.rodovia }}</td>
                            <td class="text-center">{{ passagem.km }}</td>
                            <td class="text-center">{{ passagem.uf }}</td>
                            <td class="text-center">{{ passagem.tipo_de_estrutura }}</td>
                            <td class="text-center">{{ passagem.classificacao }}</td>
                            <td class="text-center">{{ passagem.dimensoes }}</td>
                            <td class="text-center">{{ passagem.nome }}</td>
                            <td class="text-center">{{ dateTimeFormat(passagem.created_at) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
