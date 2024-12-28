function confirmRedeem(member_points, required_points, id, voucher_list_id, page) {
    if(member_points < required_points){
        alert("Not enough points to redeem, require " + (required_points - member_points) + " more points.");
    } else {
        if (confirm("Are you sure you want to redeem this voucher?")) {
            window.location.href = 'addvoucher.php?id=' + id + '&voucher_list_id=' + voucher_list_id + '&page=' + page; // Redirect to PHP script
        }
    }
}