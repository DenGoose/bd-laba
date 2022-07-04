$(document).ready(function () {
    $('.confirm-delete').click(function (e) {
        if (!confirm('Вы действительно хотите удалить?'))
        {
            e.preventDefault()
        }
    })
})