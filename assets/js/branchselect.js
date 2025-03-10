function searchBranches() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    let branches = document.getElementsByClassName('branch-item');

    for (let i = 0; i < branches.length; i++) {
        let branchName = branches[i].querySelector('.form-check-label').innerText.toLowerCase();

        if (branchName.indexOf(input) > -1) {
            branches[i].style.display = '';
        } else {
            branches[i].style.display = 'none';
        }
    }
}