/**
 * Formata data/hora para formato brasilieiro d/m/y ou para formato especificado no objeto options
 * @param dateTimeString String com dateTime
 * @param options Objeto com parametros de formação de acordo com Intl.DateTimeFormat
 */
const dateTimeFormat = (dateTimeString, options = {}) => {
    return Intl.DateTimeFormat(
        'pt-br',
        {timeZone: 'UTC', ... options}
    ).format(new Date(dateTimeString))
}

export {
    dateTimeFormat
}
