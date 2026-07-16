/**
 * Configuração dinâmica por tipo de produto
 * Define colunas, ações, etapas e características específicas de cada produto
 */

export const produtoConfig = {
  fauna: {
    nome: 'Fauna',
    temEtapas: true,
    temArquivo: true,
    temVinculacoes: false,
    colunas: ['id_campanha', 'empreendimento', 'data_inicial', 'data_final', 'status', 'subproduto'],
    acoes: ['visualizar', 'editar', 'analisar', 'arquivar', 'restaurar', 'excluir', 'aprovar', 'reprovar'],
    modalPreview: false,
    showModulo: false,
    passaModulo: null,
    rotaNome: {
      index: 'sgc.contratada.produtos.index',
      create: 'sgc.contratada.produtos.create',
      show: 'sgc.contratada.produtos.show',
      analise: 'sgc.contratada.produtos.analise',
      edit: 'sgc.contratada.produtos.edit',
      destroy: 'sgc.contratada.produtos.destroy',
      aprovarTudo: 'sgc.contratada.produtos.aprovarTudo',
      reprovarTudo: 'sgc.contratada.produtos.reprovarTudo',
      arquivar: 'sgc.contratada.produtos.arquivar',
      restaurar: 'sgc.contratada.produtos.restaurar',
    }
  },

  malarigeno: {
    nome: 'Malarígeno',
    temEtapas: false,
    temArquivo: false,
    temVinculacoes: false,
    colunas: ['id_campanha', 'data_inicial', 'status', 'subproduto'],
    acoes: ['visualizar', 'editar', 'analisar', 'arquivar', 'restaurar', 'excluir', 'aprovar', 'reprovar'],
    modalPreview: false,
    showModulo: false,
    passaModulo: null,
    rotaNome: {
      index: 'sgc.contratada.produtos.index',
      create: 'sgc.contratada.produtos.create',
      show: 'sgc.contratada.produtos.malarigeno.show',
      analise: 'sgc.contratada.produtos.malarigeno.analise',
      edit: 'sgc.contratada.produtos.malarigeno.edit',
      destroy: 'sgc.contratada.produtos.destroy',
      aprovarTudo: 'sgc.contratada.produtos.malarigeno.aprovarTudo',
      reprovarTudo: 'sgc.contratada.produtos.malarigeno.reprovarTudo',
      arquivar: null,
      restaurar: null,
    }
  },

  eia: {
    nome: 'EIA',
    temEtapas: false,
    temArquivo: false,
    temVinculacoes: true,
    colunas: ['id_campanha', 'tema', 'status_aprovacao', 'subproduto'],
    acoes: ['gerenciar', 'visualizar'],
    modalPreview: true,
    showModulo: false,
    passaModulo: null,
    rotaNome: {
      index: 'sgc.contratada.produtos.index',
      create: null,
      show: null,
      analise: null,
      edit: null,
      destroy: null,
      aprovarTudo: null,
      reprovarTudo: null,
      arquivar: null,
      restaurar: null,
      gerenciar: 'contratos.contratada.sgc.pmqa.configuracao.ponto.index',
    }
  },

  espeleologia: {
    nome: 'Espeleologia',
    temEtapas: false,
    temArquivo: false,
    temVinculacoes: false,
    colunas: ['id_campanha', 'empreendimento', 'status', 'subproduto'],
    acoes: ['visualizar', 'editar', 'arquivar', 'restaurar', 'excluir'],
    modalPreview: false,
    showModulo: false,
    passaModulo: 'espeleologia',
    rotaNome: {
      index: 'sgc.contratada.produtos.index',
      create: 'sgc.contratada.produtos.create',
      show: 'sgc.contratada.produtos.show',
      analise: null,
      edit: 'sgc.contratada.produtos.edit',
      destroy: 'sgc.contratada.produtos.destroy',
      aprovarTudo: null,
      reprovarTudo: null,
      arquivar: null,
      restaurar: null,
    }
  },

  pmqa: {
    nome: 'PMQA',
    temEtapas: false,
    temArquivo: false,
    temVinculacoes: true,
    colunas: ['id_campanha', 'tema', 'cod_emp', 'status_aprovacao', 'status', 'subproduto'],
    acoes: ['gerenciar', 'visualizar'],
    modalPreview: false,
    showModulo: false,
    passaModulo: null,
    rotaNome: {
      index: 'sgc.contratada.produtos.index',
      create: null,
      show: null,
      analise: null,
      edit: null,
      destroy: null,
      aprovarTudo: null,
      reprovarTudo: null,
      arquivar: null,
      restaurar: null,
      gerenciar: 'contratos.contratada.sgc.pmqa.configuracao.ponto.index',
    }
  },
};

/**
 * Get configuration for a product
 * @param {string} produto - Product name (e.g., 'fauna', 'malarigeno')
 * @returns {object} Product configuration
 */
export const getConfig = (produto) => {
  const key = produto.toLowerCase();
  return produtoConfig[key] || produtoConfig.fauna;
};

/**
 * Check if a product has a specific feature
 * @param {string} produto - Product name
 * @param {string} feature - Feature name
 * @returns {boolean}
 */
export const hasFeature = (produto, feature) => {
  const config = getConfig(produto);
  return config[feature] === true;
};

/**
 * Check if a product has a specific action
 * @param {string} produto - Product name
 * @param {string} action - Action name
 * @returns {boolean}
 */
export const hasAction = (produto, action) => {
  const config = getConfig(produto);
  return config.acoes.includes(action);
};
