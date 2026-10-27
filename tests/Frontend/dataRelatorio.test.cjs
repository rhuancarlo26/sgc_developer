const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync('resources/js/Pages/Sgc/Contratada/Relatorio/dataRelatorio.js', 'utf8').replace('export function', 'function');
const context = vm.createContext({ Intl, Date });
vm.runInContext(source + '; globalThis.formatar = formatarDataRelatorio;', context);
test('ISO UTC exibido com data e hora de Brasilia', () => {
  assert.equal(context.formatar('2026-10-14T13:45:54.000000Z'), '14/10/2026, 10:45:54');
  assert.equal(context.formatar('2026-10-14T01:00:00Z'), '13/10/2026, 22:00:00');
});
test('datas ausentes ou invalidas exibem texto de fallback', () => {
  for (const valor of [null, undefined, '', 'invalida']) assert.equal(context.formatar(valor, 'Sem data'), 'Sem data');
});
