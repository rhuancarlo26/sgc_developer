<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link, router, useForm} from "@inertiajs/vue3";
import {IconDeviceFloppy, IconDoorExit, IconDots} from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {useToast} from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    contrato: Object,
    rh: Object
})

const form = useForm({
    id: null,
    id_contrato: props.contrato.id,
    nome: null,
    telefone: null,
    cpf: null,
    email: null,
    profissao: null,
    funcao: null,
    ctf: null,
    ctf_validade: null,
    conselho_classe: null,
    numero_registro: null,
    status: null,
    obs: null,
    ...props.rh
});

const form_documento = useForm({
    cod_rh: props.rh.id,
    id_contrato: props.contrato.id,
    documentos: null,
    documentos_baixa: null
});

const salvarRh = () => {
    const url = form.id ? 'update' : 'store';

    form.post(route('contratos.contratada.recurso.rh.' + url), {
        onSuccess: () => form.id = props.rh.id
    });
}

const salvarDocumentoRh = () => {
    form_documento.cod_rh = props.rh.id;

    if (!form_documento.documentos) {
        return toast.info('Selecione um arquivo para enviar');
    }

    form_documento.post(route('contratos.contratada.recurso.rh.store_documento'))
}

const destroyDocumentoRh = (documento_id) => {
    router.delete(route('contratos.contratada.recurso.rh.destroy', {
        contrato: props.contrato.id,
        model_documento: documento_id
    }))
}

const salvarDocumentoBaixa = () => {
    form_documento.cod_id = props.rh.id;

    if (!form_documento.documentos_baixa) {
        return toast.info('Selecione um arquivo para enviar');
    }

    form_documento.post(route('contratos.contratada.recurso.rh.store_documento_baixa'))
}

const destroyDocumentoBaixaRh = (documento_baixa_id) => {
    router.delete(route('contratos.contratada.recurso.rh.destroy_documento_baixa', {
        contrato: props.contrato.id,
        model_documento: documento_baixa_id
    }))
}

</script>

<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.contratada.recurso.rh.index', contrato.id), label: `Rh` },
          { route: '#', label: contrato.contratada }
        ]
          "/>
                <Link class="btn btn-info" :href="route('contratos.contratada.recurso.rh.index', contrato.id)">
                    <IconDoorExit class="me-2"/>
                    Voltar
                </Link>
            </div>
        </template>

        <Navbar :contrato="contrato">

            <template #body>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="form-tab" data-bs-toggle="tab" data-bs-target="#form"
                                type="button"
                                role="tab" aria-controls="form" aria-selected="true">Formulário
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos"
                                type="button"
                                role="tab" aria-controls="documentos" aria-selected="false">Documentos
                        </button>
                    </li>
                    <!--          <li class="nav-item" role="presentation">-->
                    <!--            <button class="nav-link" id="documentos_baixa-tab" data-bs-toggle="tab" data-bs-target="#documentos_baixa"-->
                    <!--              type="button" role="tab" aria-controls="documentos_baixa" aria-selected="false">Documentos baixa</button>-->
                    <!--          </li>-->
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="form" role="tabpanel" aria-labelledby="form-tab">
                        <form @submit.prevent="salvarRh()">
                            <div class="row mb-4 mt-4">
                                <div class="col form-group">
                                    <InputLabel value="Nome" for="nome"/>
                                    <input type="text" name="nome" id="nome" class="form-control" v-model="form.nome">
                                    <InputError :message="form.errors.nome"/>
                                </div>
                                <div class="col form-group">
                                    <InputLabel value="Telefone" for="telefone"/>
                                    <input type="text" name="telefone" id="telefone" v-maska
                                           data-maska="(##) # ####-####"
                                           class="form-control" v-model="form.telefone">
                                    <InputError :message="form.errors.telefone"/>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col form-group">
                                    <InputLabel value="CPF" for="cpf"/>
                                    <input type="text" name="cpf" id="cpf" v-maska data-maska="###.###.###-##"
                                           class="form-control"
                                           v-model="form.cpf">
                                    <InputError :message="form.errors.cpf"/>
                                </div>
                                <div class="col form-group">
                                    <InputLabel value="E-mail" for="email"/>
                                    <input type="email" name="email" id="email" class="form-control"
                                           v-model="form.email">
                                    <InputError :message="form.errors.email"/>
                                </div>
                                <div class="col form-group">
                                    <InputLabel value="Formação" for="profissao"/>
                                    <input type="text" name="profissao" id="profissao" class="form-control"
                                           v-model="form.profissao">
                                    <InputError :message="form.errors.profissao"/>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col form-group">
                                    <InputLabel value="Função" for="funcao"/>
                                    <input type="text" name="funcao" id="funcao" class="form-control"
                                           v-model="form.funcao">
                                    <InputError :message="form.errors.funcao"/>
                                </div>
                                <div class="col form-group">
                                    <InputLabel value="CTF" for="ctf"/>
                                    <input type="text" name="ctf" id="ctf" class="form-control" v-model="form.ctf">
                                    <InputError :message="form.errors.ctf"/>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col form-group">
                                    <InputLabel value="Validade" for="ctf_validade"/>
                                    <input type="date" name="ctf_validade" id="ctf_validade" class="form-control"
                                           v-model="form.ctf_validade">
                                    <InputError :message="form.errors.ctf_validade"/>
                                </div>
                                <div class="col form-group">
                                    <InputLabel value="Conselho de classe" for="conselho_classe"/>
                                    <select name="conselho_classe" id="conselho_classe" class="form-control form-select"
                                            v-model="form.conselho_classe">
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                    <InputError :message="form.errors.conselho_classe"/>
                                </div>
                                <div v-if="form.conselho_classe" class="col form-group">
                                    <InputLabel value="Número de registro" for="numero_registro"/>
                                    <input type="text" name="numero_registro" id="numero_registro" class="form-control"
                                           v-model="form.numero_registro">
                                    <InputError :message="form.errors.numero_registro"/>
                                </div>
                                <div class="col form-group">
                                    <InputLabel value="Status" for="status"/>
                                    <select name="status" id="status" class="form-control form-select"
                                            v-model="form.status">
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                    <InputError :message="form.errors.status"/>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col form-group">
                                    <InputLabel value="Observação" for="observacao"/>
                                    <textarea name="observacao" id="observacao" rows="5" class="form-control"
                                              v-model="form.obs"></textarea>
                                    <InputError :message="form.errors.obserobsvacao"/>
                                </div>
                            </div>
                            <div class="mb-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-success" :disabled="form.processing">
                                    <IconDeviceFloppy class="me-2"/>
                                    {{ form.id ? 'Alterar' : 'Salvar' }}
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
                        <div v-if="rh.id">
                            <div class="row mb-4 mt-4">
                                <div class="col">
                                    <InputLabel value="Documentos" for="anexo"/>
                                    <div class="row g-2">
                                        <div class="col">
                                            <input @input="form_documento.documentos = $event.target.files" type="file"
                                                   id="anexo"
                                                   name="anexo" class="form-control" multiple>
                                        </div>
                                        <div class="col-auto">
                                            <a @click="salvarDocumentoRh()" href="javascript:void(0)"
                                               class="btn btn-success"
                                               aria-label="Button">
                                                Salvar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover non-hover">
                                    <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Açao</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="documento in rh.documentos " :key="documento.id">
                                        <td>{{ documento.nome_arquivo }}</td>
                                        <td>
                                            <button class="btn dropdown-toggle align-text-top"
                                                    data-bs-boundary="viewport"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                <IconDots/>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" target="_blank"
                                                   :href="route('contratos.contratada.recurso.rh.visualizar', documento.id)">
                                                    Visualizar
                                                </a>
                                                <a @click="destroyDocumentoRh(documento.id)" class="dropdown-item"
                                                   href="javascript:void(0)">
                                                    Excluir
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center" v-else>
                            <span
                                class="h4 mt-4">É necessário que o formulário seja salvo antes de anexar documentos.</span>
                        </div>
                    </div>
                    <!--          <div class="tab-pane fade" id="documentos_baixa" role="tabpanel" aria-labelledby="documentos_baixa-tab">-->
                    <!--            <div v-if="rh.id">-->
                    <!--              <div class="row mb-4 mt-4">-->
                    <!--                <div class="col">-->
                    <!--                  <InputLabel value="Documentos de baixa" for="baixa" />-->
                    <!--                  <div class="row g-2">-->
                    <!--                    <div class="col">-->
                    <!--                      <input @input="form_documento.documentos_baixa = $event.target.files" type="file" id="baixa"-->
                    <!--                        name="baixa" class="form-control" multiple>-->
                    <!--                    </div>-->
                    <!--                    <div class="col-auto">-->
                    <!--                      <a @click="salvarDocumentoBaixa()" href="javascript:void(0)" class="btn btn-success"-->
                    <!--                        aria-label="Button">-->
                    <!--                        Salvar-->
                    <!--                      </a>-->
                    <!--                    </div>-->
                    <!--                  </div>-->
                    <!--                </div>-->
                    <!--              </div>-->

                    <!--              <div class="table-responsive">-->
                    <!--                <table class="table table-hover non-hover">-->
                    <!--                  <thead>-->
                    <!--                    <tr>-->
                    <!--                      <th>Nome</th>-->
                    <!--                      <th>Açao</th>-->
                    <!--                    </tr>-->
                    <!--                  </thead>-->
                    <!--                  <tbody>-->
                    <!--                    <tr v-for="documento_baixa in rh.documentos_baixa " :key="documento_baixa.id">-->
                    <!--                      <td>{{ documento_baixa.nome }}</td>-->
                    <!--                      <td>-->
                    <!--                        <span class="dropdown">-->
                    <!--                          <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"-->
                    <!--                            data-bs-toggle="dropdown" aria-expanded="false">-->
                    <!--                            <IconDots />-->
                    <!--                          </button>-->
                    <!--                          <div class="dropdown-menu dropdown-menu-end">-->
                    <!--                            <a class="dropdown-item" target="_blank"-->
                    <!--                              :href="route('contratos.contratada.recurso.rh.visualizar_documento_baixa', documento_baixa.id)">-->
                    <!--                              Visualizar-->
                    <!--                            </a>-->
                    <!--                            <a @click="destroyDocumentoBaixaRh(documento_baixa.id)" class="dropdown-item"-->
                    <!--                              href="javascript:void(0)">-->
                    <!--                              Excluir-->
                    <!--                            </a>-->
                    <!--                          </div>-->
                    <!--                        </span>-->
                    <!--                      </td>-->
                    <!--                    </tr>-->
                    <!--                  </tbody>-->
                    <!--                </table>-->
                    <!--              </div>-->
                    <!--            </div>-->
                    <!--            <div class="d-flex justify-content-center" v-else>-->
                    <!--              <span class="h4 mt-4">É necessário que o formulário seja salvo antes de anexar documentos.</span>-->
                    <!--            </div>-->
                    <!--          </div>-->
                </div>
            </template>
        </Navbar>

    </AuthenticatedLayout>
</template>
