<?php if (isset($this->session->userdata['LocalFilter'])): ?>
    <p class="filterset local">A local filter has been set: 
        <?php
            $toggle = ($this->session->userdata['LocalFilterOn']) ? 'off' : 'on';
            echo anchor(site_url() . 'key/toggle_localfilter/' . $toggle, 'turn ' . $toggle);
        ?>
    </p>
<?php elseif (isset($this->session->userdata['GlobalFilter']) && $infilter): ?>
    <p class="filterset global">A global filter has been set: 
        <?php
            $toggle = ($this->session->userdata['GlobalFilterOn']) ? 'off' : 'on';
            echo anchor(site_url() . 'key/toggle_globalfilter/' . $toggle, 'turn ' . $toggle) . ' | ';
            echo anchor(site_url() . 'key/remove_globalfilter/', 'remove');
        ?>
    </p>
<?php endif; ?>
