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

};

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
