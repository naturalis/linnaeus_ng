select
	_a.id, 
	_a.taxon, 
	_c.rank,
	_d.commonname,
	_a.parent_id
from
	dev_taxa _a

left join dev_projects_ranks _b
	on _a.project_id=_b.project_id
	and _a.rank_id=_b.id

left join dev_ranks _c
	on _b.rank_id=_c.id

left join dev_commonnames _d
	on _a.project_id=_d.project_id
	and _a.id=_d.taxon_id
	and _d.language_id = 24
	
where
	_a.project_id=249
group by
	_a.id
order by
	_a.parent_id,
	_a.taxon