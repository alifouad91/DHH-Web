<?php
/* @var View $this */
/* @var $c */
/* @var FormHelper $form */
$form = Loader::helper('form');
defined('C5_EXECUTE') or die("Access Denied.");
/** @var ConcreteDashboardHelper $cdh */
$cdh = Loader::helper('concrete/dashboard');
/** @var TextHelper $th */
$th = Loader::helper('text');
$dh = Loader::helper('date');
// HELPERS
$valt = Loader::helper('validation/token');

?>


<?php echo $cdh->getDashboardPaneHeaderWrapper('Logs', false, false, false); ?>

<div class="ccm-pane-options">
    <form action="<?php echo $this->action(''); ?>">
        <div class="ccm-pane-options-permanent-search">
            <div class="row offset-bottom">
                <div class="span3">
                    <?php echo $form->label('keyword', 'Keyword'); ?>
                    <div class="controls">
                        <?php echo $form->text('keyword'); ?>
                    </div>
                </div>
                <div class="span3">
                    <?php echo $form->label('user', 'User'); ?>
                    <div class="controls">
                        <?php echo $form->select('userId', $userArr, ['style' => 'width: 220px;']); ?>
                    </div>
                </div>
                <div class="span3">
                    <?php echo $form->label(false, '&nbsp;'); ?>
                    <div class="controls">
                        <?php echo $form->submit(false, 'Search'); ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="ccm-pane-body">
    <div class="ccm-list-wrapper">
        <table border="0" cellspacing="0" cellpadding="0" class="ccm-results-list">
            <tr>
                <th>Date/Time</th>
                <th>User</th>
                <th>Text <input style="float: right" class="btn error btn-mini" type="button"
                                onclick="if (confirm('<?php echo t('Are you sure you want to clear this log?')?>'))
                                { location.href='<?php echo View::url($configURL.'/clear/'.$valt->generate())?>'}"
                                value="<?php echo t('Clear Log')?>" />
                </th>
            </tr>
            <?php foreach ($userLogs as $i => $userLogs) { ?>
                <?php
                /* @var Property $property */
                $message           = $userLogs->getMessage();
                $createdAt           = $dh->formatPrettyDateTime($userLogs->getCreatedAt(), false, true);
                ?>
                <tr class="ccm-list-record <?php if ($i % 2 === 1) echo 'ccm-list-record-alt'; ?>">
                    <td valign="top" style="white-space: nowrap" class="active"><?php echo $createdAt; ?></td>
                    <td><?php
                        $uID = $userLogs->getUID();
                        if(empty($uID)) {
                            echo t('Guest');
                        }
                        else {
                            $u = User::getByUserID($uID);
                            if(is_object($u)) {
                                echo $u->getUserName();
                            }
                            else {
                                echo tc('Deleted user', 'Deleted (id: %s)', $uID);
                            }
                        } ?></td>
                    <td><?php echo nl2br($message); ?></td>
                </tr>
            <?php } ?>
        </table>

    </div>
</div>

