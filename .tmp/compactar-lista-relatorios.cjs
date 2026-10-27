const fs=require('fs');
const files=['D:/SGC_DEVELOPER/resources/js/Pages/Sgc/Contratada/Relatorio/Relatorios.vue','D:/Ecossistema-v2/resources/js/Pages/Sgc/Contratada/Relatorio/Relatorios.vue'];
const updates=files.map(file=>{
let s=fs.readFileSync(file,'utf8').replace(/\r\n/g,'\n');
s=s.replace('IconDoorEnter, IconPencil','IconDoorEnter, IconPencil, IconInfoCircle');
const scriptAnchor='const modalPeriodo = ref(null);';if(!s.includes(scriptAnchor))throw new Error('Script anchor');
s=s.replace(scriptAnchor,`const modalAprovacao = ref(null);
const aprovacaoSelecionada = ref(null);
const verAprovacao = relatorio => {
  aprovacaoSelecionada.value = { numero: relatorio.relatorio_num, dados: relatorio.aprovacao };
  modalAprovacao.value.getBsModal().show();
};

`+scriptAnchor);
const first=s.indexOf('                          <template v-if="relatorio.aprovacao">');const last=s.indexOf('                          </template>',first)+'</template>'.length+26;
if(first<0||last<26)throw new Error('Approval anchor');
s=s.slice(0,first)+`                          <button v-if="relatorio.aprovacao" type="button" class="aprovacao-resumo" :aria-label="'Ver detalhes da aprova\u00e7\u00e3o do relat\u00f3rio ' + relatorio.relatorio_num" title="Ver respons\u00e1vel e data da aprova\u00e7\u00e3o" @click="verAprovacao(relatorio)">
                            <span>{{ relatorio.aprovacao.user?.name || 'Ver aprova\u00e7\u00e3o' }}</span>
                            <IconInfoCircle :size="15" aria-hidden="true" />
                          </button>`+s.slice(last);
const modalAnchor='    <AuthenticatedLayout>';if(!s.includes(modalAnchor))throw new Error('Modal anchor');
s=s.replace(modalAnchor,`    <Modal ref="modalAprovacao" :title="'Aprova\u00e7\u00e3o \u00b7 Relat\u00f3rio ' + (aprovacaoSelecionada?.numero || '')" modalDialogClass="modal-dialog-centered">
      <template #body>
        <dl v-if="aprovacaoSelecionada" class="mb-0">
          <dt class="text-muted small mb-1">Aprovado por</dt>
          <dd class="mb-3">{{ aprovacaoSelecionada.dados.user?.name || 'Respons\u00e1vel n\u00e3o registrado' }}</dd>
          <dt class="text-muted small mb-1">Data e hora</dt>
          <dd class="mb-0">{{ formatarDataRelatorio(aprovacaoSelecionada.dados.created_at, 'Data n\u00e3o registrada') }}</dd>
        </dl>
      </template>
    </Modal>
`+modalAnchor);
s=s.replace('height: 64px; font-size: .8125rem;', 'height: 50px; padding-top: 8px; padding-bottom: 8px; font-size: .8125rem;');
s=s.replace(/^\.aprovacao-responsavel[^\n]*\n\.aprovacao-data[^\n]*\n/m,`.aprovacao-resumo { display: inline-flex; align-items: center; gap: 6px; max-width: 100%; padding: 2px 0; border: 0; background: transparent; color: #334155; font: inherit; cursor: pointer; }
.aprovacao-resumo span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.aprovacao-resumo :deep(svg) { flex-shrink: 0; color: #64748b; }
.aprovacao-resumo:hover { color: #2563eb; }
.aprovacao-resumo:focus-visible { outline: 2px solid #3b82f6; outline-offset: 3px; border-radius: 3px; }
`);
return [file,s];
});const vue=require('D:/SGC_DEVELOPER/node_modules/@vue/compiler-sfc');for(const [file,s]of updates){const {descriptor,errors}=vue.parse(s);if(errors.length)throw errors;const script=vue.compileScript(descriptor,{id:'relatorios'});const tpl=vue.compileTemplate({source:descriptor.template.content,filename:file,id:'relatorios',compilerOptions:{bindingMetadata:script.bindings}});if(tpl.errors.length)throw tpl.errors;for(const style of descriptor.styles){const css=vue.compileStyle({source:style.content,filename:file,id:'relatorios',scoped:true});if(css.errors.length)throw css.errors;}}for(const [file,s]of updates){fs.writeFileSync(file,s);console.log(file);}
