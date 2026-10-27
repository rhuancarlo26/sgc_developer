const fs=require('fs'), cp=require('child_process'), assert=require('node:assert/strict');
const base='D:/Ecossistema-v2';const git=(...args)=>cp.execFileSync('git',['-c','safe.directory=D:/Ecossistema-v2','-C',base,...args],{encoding:'utf8'});
const allowed=new Set(JSON.parse(fs.readFileSync('D:/SGC_DEVELOPER/.tmp/relatorio-port-files.json','utf8')));
const changed=git('status','--porcelain','--untracked-files=all').trim().split('\n').filter(Boolean).map(line=>line.trim().slice(2).trim());
for(const file of changed)assert.ok(allowed.has(file),'Outside scope: '+file);
assert.equal(changed.length,allowed.size);assert.equal(git('branch','--show-current').trim(),'fix/SGC-relatorio-coordenacao');
assert.equal(git('diff','--','composer.json','composer.lock','package.json','package-lock.json','database/migrations'), '');
assert.ok(!changed.includes('.env'));
console.log('Escopo confirmado: '+changed.length+' arquivos; branch correta; dependencias/schema intactos; nenhum .env no diff.');
