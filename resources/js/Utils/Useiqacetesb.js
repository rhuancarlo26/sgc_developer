/**
 * IQA CETESB — Índice de Qualidade das Águas
 * Fórmula: IQA = ∏ qi^wi  (produto ponderado)
 *
 * Mapeamento baseado nos dados reais do sistema:
 *   id:3  sigla:TEMP  → temperatura
 *   id:5  sigla:""    → nitrogenio   (pelo nome)
 *   id:7  sigla:OD    → od           (vem em mg/L → converte via temperatura)
 *   id:8  sigla:SDT   → residuo      (Sólidos Dissolvidos Totais ≈ Resíduo Total)
 *   id:9  sigla:TURB  → turbidez
 *   id:10 sigla:DBO   → dbo
 *   id:12 sigla:""    → coliformes   (pelo nome)
 *   id:20 sigla:P     → fosforo
 *   id:21 sigla:""    → ph           (pelo nome "PH")
 */

// ---------------------------------------------------------------------------
// Mapeamento sigla → chave interna
// ---------------------------------------------------------------------------
const MAPA_SIGLA = {
    OD:   'od',
    DBO:  'dbo',
    DBO5: 'dbo',
    TURB: 'turbidez',
    NTU:  'turbidez',
    TEMP: 'temperatura',
    SDT:  'residuo',
    ST:   'residuo',
    RT:   'residuo',
    P:    'fosforo',
    PT:   'fosforo',
    NT:   'nitrogenio',
    N:    'nitrogenio',
    CT:   'coliformes',
    CF:   'coliformes',
};

// ---------------------------------------------------------------------------
// Mapeamento por nome (fallback quando sigla está vazia)
// ---------------------------------------------------------------------------
const MAPA_NOME = [
    { chave: 'od',          termos: ['oxigênio dissolvido', 'oxigenio dissolvido', 'o.d.'] },
    { chave: 'dbo',         termos: ['demanda bioquímica', 'demanda bioquimica', 'dbo'] },
    { chave: 'turbidez',    termos: ['turbidez'] },
    { chave: 'temperatura', termos: ['temperatura'] },
    { chave: 'residuo',     termos: ['sólidos dissolvidos', 'solidos dissolvidos', 'sólidos totais', 'solidos totais', 'resíduo', 'residuo'] },
    { chave: 'fosforo',     termos: ['fósforo', 'fosforo'] },
    { chave: 'nitrogenio',  termos: ['nitrogênio', 'nitrogenio'] },
    { chave: 'coliformes',  termos: ['coliformes'] },
    { chave: 'ph',          termos: ['ph', 'potencial hidrog'] },
];

/**
 * Recebe o objeto `parametro` do backend ({ sigla, parametro, unidade, ... })
 * e retorna a chave interna IQA ou null.
 */
export function detectarChaveIqa(parametro) {
    if (!parametro) return null;

    // 1. Tenta pela sigla (mais confiável)
    const sigla = (parametro.sigla ?? '').trim().toUpperCase();
    if (sigla && MAPA_SIGLA[sigla]) return MAPA_SIGLA[sigla];

    // 2. Fallback pelo nome
    const nome = (parametro.parametro ?? '').toLowerCase().trim();
    for (const { chave, termos } of MAPA_NOME) {
        if (termos.some(t => nome.includes(t))) return chave;
    }

    return null;
}

// ---------------------------------------------------------------------------
// Pesos wi (CETESB)
// ---------------------------------------------------------------------------
export const PESOS_IQA = {
    od:          0.17,
    coliformes:  0.15,
    ph:          0.12,
    dbo:         0.10,
    temperatura: 0.10,
    nitrogenio:  0.10,
    fosforo:     0.10,
    turbidez:    0.08,
    residuo:     0.08,
};

// ---------------------------------------------------------------------------
// Interpolação linear entre pontos tabelados
// ---------------------------------------------------------------------------
function interpolar(pontos, x) {
    if (x <= pontos[0][0])                 return pontos[0][1];
    if (x >= pontos[pontos.length - 1][0]) return pontos[pontos.length - 1][1];
    for (let i = 0; i < pontos.length - 1; i++) {
        const [x0, y0] = pontos[i];
        const [x1, y1] = pontos[i + 1];
        if (x >= x0 && x <= x1) return y0 + (y1 - y0) * (x - x0) / (x1 - x0);
    }
    return 0;
}

// ---------------------------------------------------------------------------
// Curvas qi — CETESB
// ---------------------------------------------------------------------------
const C_OD = [
    [0,2],[10,5],[20,10],[30,20],[40,30],[50,45],
    [60,58],[70,70],[80,80],[88,90],[100,97],[110,95],[120,87],[140,72],
];
const C_COLI_LOG = [
    [0,100],[1,88],[2,72],[2.7,55],[3,40],[3.7,20],[4,8],[5,2],
];
const C_PH = [
    [2,2],[4,10],[5,30],[6,52],[7,90],[7.5,95],[8,90],[9,52],[10,20],[12,2],
];
const C_DBO = [
    [0,99],[1,96],[2,90],[3,82],[5,68],[8,50],[10,42],[15,25],[20,15],[30,5],
];
const C_TEMP = [
    [0,93],[1,88],[2,80],[3,70],[4,58],[5,46],[7,30],[10,15],[15,5],
];
const C_NIT = [
    [0,99],[0.5,90],[1,78],[2,60],[3,48],[5,30],[8,18],[10,12],[15,6],[20,2],
];
const C_FOS = [
    [0,99],[0.05,90],[0.1,78],[0.2,62],[0.3,52],[0.5,38],[1,22],[2,10],[5,3],
];
const C_TURB = [
    [0,99],[5,90],[10,80],[20,68],[30,58],[40,50],[50,44],
    [75,34],[100,26],[150,18],[200,12],[300,6],
];
const C_RES = [
    [0,82],[100,79],[200,75],[300,70],[400,63],[500,55],
    [600,46],[700,38],[800,30],[900,22],[1000,15],
];

// ---------------------------------------------------------------------------
// Saturação do OD em mg/L — Benson & Krause (1984)
// Usada para converter OD mg/L → % saturação
// ---------------------------------------------------------------------------
export function odSaturacao(tempC) {
    return 14.62 - 0.3898 * tempC + 0.006969 * tempC ** 2 - 5.696e-5 * tempC ** 3;
}

// ---------------------------------------------------------------------------
// qi individuais
// ---------------------------------------------------------------------------
const CALC_QI = {
    od:          v => interpolar(C_OD, v),
    coliformes:  v => v <= 1 ? 100 : Math.max(0, interpolar(C_COLI_LOG, Math.log10(v))),
    ph:          v => interpolar(C_PH, v),
    dbo:         v => interpolar(C_DBO, v),
    temperatura: v => interpolar(C_TEMP, Math.abs(v)),
    nitrogenio:  v => interpolar(C_NIT, v),
    fosforo:     v => interpolar(C_FOS, v),
    turbidez:    v => interpolar(C_TURB, v),
    residuo:     v => interpolar(C_RES, v),
};

// ---------------------------------------------------------------------------
// Função principal
// ---------------------------------------------------------------------------

/**
 * Calcula o IQA CETESB.
 *
 * @param {Object} valores  { od, coliformes, ph, dbo, temperatura, nitrogenio, fosforo, turbidez, residuo }
 *                          Apenas os parâmetros disponíveis — ausentes são ignorados
 *                          e pesos são normalizados automaticamente.
 *
 * @param {Object} [opts]
 *   odEmMgL  {boolean} — OD vem em mg/L? (padrão: true — unidade 'mg/L O2')
 *   tempAgua {number}  — temperatura de referência em °C para conversão OD
 *                        (padrão: usa o valor de 'temperatura' se disponível, senão 25°C)
 *
 * @returns {{ iqa: number|null, qi: Object, parametrosUsados: string[], somaPesos: number }}
 */
export function calcularIqa(valores, { odEmMgL = true, tempAgua = null } = {}) {
    const toFloat = v => parseFloat(String(v ?? '').replace(',', '.'));

    // Temperatura de referência para conversão do OD
    const tempRef = tempAgua ?? (valores.temperatura ? toFloat(valores.temperatura) : 25);

    let produto   = 1;
    let somaPesos = 0;
    const qi      = {};
    const usados  = [];

    for (const [chave, calcFn] of Object.entries(CALC_QI)) {
        const raw = valores[chave];
        if (raw === null || raw === undefined || raw === '') continue;

        let val = toFloat(raw);
        if (isNaN(val)) continue;

        // OD mg/L → % saturação
        if (chave === 'od' && odEmMgL) {
            val = (val / odSaturacao(tempRef)) * 100;
        }

        const qiVal = Math.max(0, Math.min(100, calcFn(val)));
        const wi    = PESOS_IQA[chave];

        qi[chave] = +qiVal.toFixed(2);
        produto   *= Math.pow(qiVal > 0 ? qiVal : 0.001, wi);
        somaPesos += wi;
        usados.push(chave);
    }

    if (usados.length === 0) return { iqa: null, qi, parametrosUsados: usados, somaPesos: 0 };

    // Normaliza pesos quando nem todos os 9 parâmetros estão presentes
    const iqaBruto = somaPesos >= 1
        ? produto
        : Math.pow(produto, 1 / somaPesos);

    return {
        iqa:             +(Math.min(100, iqaBruto)).toFixed(1),
        qi,
        parametrosUsados: usados,
        somaPesos:        +somaPesos.toFixed(2),
    };
}

// ---------------------------------------------------------------------------
// Classificação qualitativa CETESB
// ---------------------------------------------------------------------------
export function classificarIqa(iqa) {
    if (iqa === null || iqa === undefined) return null;
    if (iqa > 79) return { classe: 'Ótima',   faixa: '79–100', cor: '#1565C0', badge: 'primary' };
    if (iqa > 51) return { classe: 'Boa',      faixa: '51–79',  cor: '#2E7D32', badge: 'success' };
    if (iqa > 36) return { classe: 'Razoável', faixa: '36–51',  cor: '#F57F17', badge: 'warning' };
    if (iqa > 19) return { classe: 'Ruim',     faixa: '19–36',  cor: '#BF360C', badge: 'danger'  };
    return             { classe: 'Péssima',  faixa: '0–19',   cor: '#7B1FA2', badge: 'danger'  };
}
