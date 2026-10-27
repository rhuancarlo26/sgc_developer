const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

for (const arquivo of ['DocxModal.vue', 'DocxHistorico.vue']) {
    for (const versao of [0, 2, 5]) {
        test(arquivo + ' solicita exatamente a revisao ' + versao, async () => {
            const source = fs.readFileSync('resources/js/Pages/Sgc/Contratada/Relatorio/' + arquivo, 'utf8');
            const trecho = source.slice(source.indexOf("const erroDocumento ="), source.indexOf('const enableCounter'));
            const chamadas = [];
            const renderizados = [];
            const context = {
                ref: value => ({ value }), nextTick: async () => {},
                modalKey: { value: 0 }, wordDocument: { value: null },
                modalDetalhes: { value: { getBsModal: () => ({ show() {} }) } },
                docModal: { value: {} }, notes: { value: [] }, loadComments() {},
                props: { numRelatorio: 18 }, console: { error() {} },
                route: (name, params) => { chamadas.push({ name, params }); return '/arquivo'; },
                fetch: async () => ({ ok: true, blob: async () => 'conteudo' }),
                renderAsync: async blob => renderizados.push(blob),
            };
            vm.createContext(context);
            vm.runInContext(trecho + '; globalThis.abrir = abrirModal;', context);
            await context.abrir(1, 6, versao);
            assert.equal(chamadas.length, 1);
            assert.equal(chamadas[0].name, 'sgc.contratada.get_docx');
            assert.equal(chamadas[0].params.versao, versao);
            assert.equal(chamadas[0].params.contratoId, 6);
            assert.equal(chamadas[0].params.numRelatorio, 18);
            assert.deepEqual(renderizados, ['conteudo']);
        });
    }
}
