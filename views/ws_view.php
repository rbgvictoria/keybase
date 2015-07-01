<?php require_once 'header.php'; ?>
<h2>Web services</h2>

<h3>Projects</h3>
<p><span class="grey"><?=site_url()?>ws/projects[?project=&laquo;<i>projectID</i>&raquo;[&items=<u>true</u>|false][&keys=<u>true</u>|false]]</span></p>
<div>Examples</div>
<ul>
    <li><?=anchor(site_url() . 'ws/projects', site_url() . 'ws/projects'); ?></li>
    <li><?=anchor(site_url() . 'ws/projects?project=10', site_url() . 'ws/projects?project=10'); ?></li>
    <li><?=anchor(site_url() . 'ws/projects?project=1&items=false', site_url() . 'ws/projects?project=1&items=false'); ?></li>
    <li><?=anchor(site_url() . 'ws/projects?project=1&keys=false', site_url() . 'ws/projects?project=1&keys=false'); ?></li>
</ul>
<p>If no arguments are passed this web service will provide a list of projects with project detail, but without keys or items. When a project ID is passed details
for a single project will be returned including all the keys and items in the project. Keys and items may be left out by passing 'keys=false' or 'items=false' respectively.</p>
<p>Returned result contains key hierarchy, so, depending on performance of the client-side processing, this web service could eventually replace the alphabetical
and hierarchical project keys web services that KeyBase currently uses internally.</p>
<p>There is some overlap between this web service and the Items and Keys web services below, but the latter can also take a taxonomic scope argument instead of a project ID and
allow for CSV output, while this web service only provides JSON.</p>

<h3>Items</h3>
<p><span class="grey"><?=site_url()?>ws/items?project=&laquo;<i>projectID</i>&raquo;|key=&laquo;<i>keyID</i>&raquo;[&format=<u>json</u>|csv][&pageSize=&laquo;<i>page size</i>&raquo;][&page=&laquo;<i>page</i>&raquo;]</span></p>
<div>Examples:</div>
<ul>
    <li><?=anchor(site_url() . 'ws/items?project=10', site_url() . 'ws/items?project=10'); ?></li>
    <li><?=anchor(site_url() . 'ws/items?key=8', site_url() . 'ws/items?key=8'); ?></li>
</ul>

<h3>Keys</h3>
<p><span class="grey"><?=site_url()?>ws/keys?project=&laquo;<i>projectID</i>&raquo;|tscope=&laquo;<i>taxon name</i>&raquo;[&format=<u>json</u>|csv][&pageSize=&laquo;<i>page size</i>&raquo;][&page=&laquo;<i>page</i>&raquo;]</span></p>
<div>Examples:</div>
<ul>
    <li><?=anchor(site_url() . 'ws/keys?project=12', site_url() . 'ws/keys?project=12'); ?></li>
    <li><?=anchor(site_url() . 'ws/keys?tscope=Banksia', site_url() . 'ws/keys?tscope=Banksia'); ?></li>
</ul>

<h3>Key</h3>
<p><?=site_url()?>ws/keyJSON?key_id=&laquo;<i>keyID</i>&raquo;</p>
<p>Example:</p>
<ul>
    <li><?=anchor(site_url() . 'ws/keyJSON?key_id=2413')?></li>
</ul>

<h3>Filter</h3>
<p><span class="grey"><?=site_url()?>ws/filter?id=&laquo;<i>filterID</i>&raquo;|name=&laquo;<i>filterName</i>&raquo;[&format=<u>json</u>|xml]</span></p>
<div>Examples:</div>
<ul>
    <li><?=anchor(site_url() . 'ws/filter?id=54f98c14457b8', site_url() . 'ws/filter?id=54f98c14457b8'); ?></li>
    <li><?=anchor(site_url() . 'ws/filter?id=54f98c14457b8&format=json', site_url() . 'ws/filter?id=54f98c14457b8&format=json'); ?></li>
    <li><?=anchor(site_url() . 'ws/filter?id=54f98c14457b8&format=xml', site_url() . 'ws/filter?id=54f98c14457b8&format=xml'); ?></li>
</ul>

<h3>Global filter</h3>
<p><span class="grey"><?=site_url()?>ws/globalFilter?filter_id=&laquo;<i>filter ID</i>&raquo</span></p>
<div>Examples:</div>
<ul>
    <li><?=anchor(site_url() . 'ws/globalFilter?filter_id=54f98c14457b8&projects=10', site_url() . 'ws/globalFilter?filter_id=54f98c14457b8'); ?></li>
    <li><?=anchor(site_url() . 'ws/globalFilter?filter_id=555f5b4bd9412&projects=11', site_url() . 'ws/globalFilter?filter_id=555f5b4bd9412'); ?></li>
</ul>
<p>This service does basically the same as the previous one, only it doesn't need the nested sets and can create global filters on the fly from a stored list 
    of item IDs, while the previous service merely retrieved a stored array.</p>


<h2>Export</h2>
<h3>Key</h3>
<p><?=site_url()?>key/export/&laquo;<i>format</i>&raquo;/&laquo;<i>keyID</i>&raquo;</p>
<p>&laquo;<i>format</i>&raquo;: lpxk|sdd|csv|txt</p>
<p>Examples</p>
<ul>
    <li><?=anchor(site_url() . 'key/export/lpxk/2672');?></li>
    <li><?=anchor(site_url() . 'key/export/sdd/2672');?></li>
    <li><?=anchor(site_url() . 'key/export/csv/2672');?></li>
    <li><?=anchor(site_url() . 'key/export/txt/2672');?></li>
</ul>

<h2>AJAX</h2>
<p>Used internally</p>

<h3>Project keys</h3>
<h4>Hierarchical</h4>
<p><span class="grey"><?=site_url()?>ajax/projectkeys_hierarchy/&laquo;<i>projectID</i>&raquo;[/&laquo;<i>filterID</i>&raquo;];</span></p>
<p>Examples:</p>
<ul> 
    <li><?=anchor(site_url() . 'ajax/projectkeys_hierarchy/10');?></li>
    <li><?=anchor(site_url() . 'ajax/projectkeys_hierarchy/10/54f98c14457b8');?></li>
</ul>

<h4>Alphabetical</h4>
<p><span class="grey"><?=site_url()?>ajax/projectkeys_alphabetical/&laquo;<i>projectID</i>&raquo;[/&laquo;<i>filterID</i>&raquo;];</span></p>
<p>Example:</p>
<ul>
    <li><?=anchor(site_url() . 'ajax/projectkeys_alphabetical/10');?></li>
</ul>

<h3>Couplet (used for key player)</h3>
<h4>Current node</h4>
<p><span class="grey"><?=site_url()?>ajax/nextCoupletJSON/&laquo;<i>keyID</i>&raquo;/&laquo;<i>leadID</i>&raquo;</span></p>
<p>Example:</p>
<ul>
    <li><?=anchor(site_url() . 'ajax/nextCoupletJSON/2672/241183');?></li>
</ul> 

<h4>Path</h4>
<p><span class="grey"><?=site_url()?>ajax/pathJSON/&laquo;<i>keyID</i>&raquo;/&laquo;<i>leadID</i>&raquo;</span></p>
<p>Example:</p>
<ul>
    <li><?=anchor(site_url() . 'ajax/pathJSON/2672/241183');?></li>
</ul>

<h4>Remaining items</h4>
<p><span class="grey"><?=site_url()?>ajax/remainingItemsJSON/&laquo;<i>keyID</i>&raquo;/&laquo;<i>leadID</i>&raquo;</span></p>
<p>Example:</p>
<ul>
    <li><?=anchor(site_url() . 'ajax/remainingItemsJSON/2672/241183');?></li>
</ul>

<h4>Discarded items</h4>
<p><span class="grey"><?=site_url()?>ajax/discardedItemsJSON/&laquo;<i>keyID</i>&raquo;/&laquo;<i>leadID</i>&raquo;</span></p>
<p>Example:</p>
<ul>
    <li><?=anchor(site_url() . 'ajax/discardedItemsJSON/2672/241183');?></li>
</ul>

<h4>Entire couplet</h4>
<p><span class="grey"><?=site_url()?>ajax/coupletJSON/&laquo;<i>keyID</i>&raquo;/&laquo;<i>leadID</i>&raquo;</span></p>
<p>Example:</p>
<ul>
    <li><?=anchor(site_url() . 'ajax/coupletJSON/2672/241183');?></li>
</ul>

<?php require_once 'footer.php'; ?>