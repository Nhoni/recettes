document.addEventListener('DOMContentLoaded', (event) => {
    const stars = document.querySelectorAll('.star-rating .fa');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            document.querySelector('input[name="mark[mark]"]').value = rating;
            stars.forEach(s => s.classList.remove('checked'));
            this.classList.add('checked');
            this.previousElementSibling?.classList.add('checked');
            if (this.nextElementSibling) {
                this.nextElementSibling.classList.remove('checked');
            }
        });
    });
});