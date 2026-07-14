const capitalize = (text) => {
    return text.charAt(0).toUpperCase() + text.slice(1);
}

const withoutSpecialChars = (string) => {
    return string?.replace(/[^a-zA-Z0-9 ]/, '');
}

export {
    capitalize,
    withoutSpecialChars
}
