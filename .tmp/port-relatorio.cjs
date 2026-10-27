const fs=require('fs'), path=require('path'), cp=require('child_process');
const src='D:/SGC_DEVELOPER', dst='D:/Ecossistema-v2';
if(fs.realpathSync(dst).replaceAll('\\','/').toLowerCase()!=='d:/ecossistema-v2')throw new Error('Target mismatch');
const git=(...args)=>cp.execFileSync('git',['-c','safe.directory=D:/Ecossistema-v2','-C',dst,...args],{encoding:'utf8'}).trim();
if(git('branch','--show-current')!=='fix/SGC-relatorio-coordenacao'||git('status','--porcelain'))throw new Error('Branch must be clean');
const files=JSON.parse(fs.readFileSync(src+'/.tmp/relatorio-port-files.json','utf8'));
for(const file of files){
let content;
if(file.endsWith('Routes/ContratadaRoutes.php')){
content=fs.readFileSync(dst+'/'+file,'utf8');
const marker='    // Download anexo';if(!content.includes(marker))throw new Error('Missing route anchor');
content=content.replace(marker,"    Route::patch('{contrato}/relatorio/{relatorio_num}/periodo', [RelatorioCoordenacaoController::class, 'atualizarPeriodo'])->name('sgc.contratada.relatorio.periodo.update');\n\n"+marker);
}else if(file.endsWith('NavbarContrato.vue')){
content=fs.readFileSync(dst+'/'+file,'utf8');const marker=":route-name=\"'sgc.contratada.relatorios.index'\"";if(!content.includes(marker))throw new Error('Missing menu anchor');content=content.replace(marker,marker+'\n            active-on-route-prefix=\"sgc.contratada.relatorio*\"');
}else{
content=fs.readFileSync(src+'/'+file,'utf8');
if(['Historico.vue','Index.vue','Relatorios.vue'].includes(path.basename(file)))content=content.replaceAll(':deep(> .d-flex > .col-md-1)',':deep(> .d-flex > :first-child)').replaceAll(':deep(> .d-flex > .col-md-11)',':deep(> .d-flex > :last-child)');
}
fs.mkdirSync(path.dirname(dst+'/'+file),{recursive:true});fs.writeFileSync(dst+'/'+file,content);console.log(file);
}
console.log('Transferred '+files.length+' files');
