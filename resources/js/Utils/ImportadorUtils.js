const badgeStatus = (status) => {

    const obj = {
        1: 'text-bg-secondary',
        2: 'text-bg-light',
        3: 'text-bg-danger',
        4: 'text-bg-primary',
    }

    return obj[status]
}

export {
    badgeStatus
}
