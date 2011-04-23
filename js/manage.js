function addMeta( i ) {
    var thead = document.getElementById("book-meta-table-" + i);
	
    var tr = document.createElement("tr");
    var ktd = document.createElement("td");
    var vtd = document.createElement("td");
	
    var k = document.createElement("textarea");
    k.className = "key";
    k.name = "keys-" + i + "[]";
	
    var v = document.createElement("textarea");
    v.className = "value";
    v.name = "values-" + i + "[]";
	
    ktd.appendChild(k);
    vtd.appendChild(v);
	
    tr.appendChild(ktd);
    tr.appendChild(vtd);
	
    thead.appendChild(tr);
}

function reviewBigger( i ) {
    var height = jQuery("#review-" + i).css("height");
    jQuery("#review-" + i).css("height",parseInt(height)+75+"px");
}

function reviewSmaller( i ) {
    var height = jQuery("#review-" + i).css("height");
    if ( height - 75 > 0 )
        jQuery("#review-" + i).css("height",parseInt(height)+75+"px");
}