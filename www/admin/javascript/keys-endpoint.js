var keyRanks = Array();
var keyRankBorder = false;

function keyAddRank(id,rank)
{
	keyRanks[keyRanks.length] = [id,rank];
}

function keyMoveBorder(id)
{
	keyRankBorder = id;
	keyShowRanks();
}

function keyShowRanks()
{
	var first = true;
	var b= '';

	if (keyRankBorder==false) keyRankBorder = keyRanks[0][0];

	for (var i=0;i<keyRanks.length;i++) {

		if (keyRanks[i][0]==keyRankBorder) {

			b = b + 
					'<tr>'+
						'<td id="sub1" colspan="2" class="rankRedLine"></td>'+
						'<td class="rankRedLineEnd"></td>'+
					'</tr>'+
					"\n";

		}

		b = b + 
			'<tr class="tr-highlight">'+
				'<td colspan="2" class="rankSelectedRank" rankId="'+keyRanks[i][0]+'" '+
					'ondblclick="taxonRemoveRank(this.attributes.rankId.value)">'+
					keyRanks[i][1]+
				'</td>'+
				'<td>'+
					(keyRanks[i+1]!=undefined && keyRanks[i+1][0]==keyRankBorder ? 
						'<span class="rankArrow" onclick="keyMoveBorder('+keyRanks[i][0]+');">&uarr;</span>' : 
						'' )+
					(i<keyRanks.length-1 && keyRanks[i+1]!=undefined && keyRanks[i][0]==keyRankBorder ? 
						'<span class="rankArrow" onclick="keyMoveBorder('+keyRanks[i+1][0]+');">&darr;</span>' : 
						'')+
				'</td>'+
			'</tr>'+
			"\n";


	}

	$('#selected-ranks').html('<table id="selectedRanksTable">'+b+
		'</table><input type="hidden" name="keyRankBorder" value="'+keyRankBorder+'">');
}
