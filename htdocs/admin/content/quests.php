<?php

use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;

if ($sub === 'list') {
    /** @var \EtoA\Quest\QuestPresenter $questPresenter */
    $questPresenter = $app['etoa.quest.presenter'];
    echo '
    <h2>Quests</h2>

    <table width="100%" cellpadding="3" cellspacing="1" align="center">
        <tbody>
            <tr>
                <th valign="top" class="tbltitle">ID</th>
                <th valign="top" class="tbltitle">Titel</th>
                <th valign="top" class="tbltitle">Beschreibung</th>
                <th valign="top" class="tbltitle">Bedingung</th>
                <th valign="top" class="tbltitle">Tasks</th>
                <th valign="top" class="tbltitle">Belohnung</th>
            </tr>';
    foreach ($app['cubicle.quests.quests'] as $quest) {
        echo '
        <tr>
            <td class="tbldata">' . $quest['id'].'</td>
            <td class="tbldata">' . $quest['title'].'</td>
            <td class="tbldata">' . $quest['description'].'</td>
            <td class="tbldata">' . (isset($quest['trigger']) ? $quest['trigger']['type'] . ' ' . $quest['trigger']['operator'] . ' ' . $quest['trigger']['value'] : '').'</td>
            <td class="tbldata">' . $quest['task']['type'] . ' ' . $quest['task']['operator'] . ' ' . $quest['task']['value'] . '</td>
            <td class="tbldata">' . implode("\n", $questPresenter->buildRewards($quest)) .'</td>
        </tr>';
    }
    echo '</tbody></table>';
} else {
    $tpl->assign('title', 'Quests');

    $getParam = function ($value) {
        if (trim($value) === '') {
            return null;
        }

        return $value;
    };

    echo '
        <form action="?page='.$page.'&amp;sub='.$sub.'&amp;action=search" method="post">
            <table class="tbl">
                <tr>
                    <td class="tbltitle">Spieler ID</td>
                    <td class="tbldata">
                        <input type="text" name="user_id" size="20" maxlength="250" />
                    </td>
                </tr>
                <tr>
                    <td class="tbltitle">Spieler Nick</td>
                    <td class="tbldata">
                        <input type="text" name="user_nick" value="" size="20" maxlength="250" autocomplete="off" />
                    </td>
                </tr>
                <tr>
                    <td class="tbltitle">Quest</td>
                    <td class="tbldata">
                        <select name="quest_id">
                            <option value=""><i>---</i></option>';
    foreach ($app['cubicle.quests.quests'] as $quest) {
        echo '<option value=' .$quest['id']. '>' .$quest['title']. '</option>';
    }

    echo '              </select>
                    </td>
                </tr>
                <tr>
                    <td class="tbltitle">Status</td>
                    <td class="tbldata">
                        <select name="quest_state">
                            <option value=""><i>---</i></option>';
    foreach (\LittleCubicleGames\Quests\Workflow\QuestDefinition::STATES  as $state) {
        echo '<option value="' . $state. '">' . $state . '</option>';
    }

    echo '              </select>
                    </td>
                </tr>
            </table>
            <br/>
            <input type="submit" name="quest_search" value="Suche starten" />
        </form>';

    if (isset($_POST['quest_search'])) {
        /** @var \EtoA\Quest\QuestRepository $repository */
        $repository = $app['etoa.quest.repository'];
        $questDefinitions = $app['cubicle.quests.quests'];
        $questMap = [];
        foreach ($questDefinitions as $questDefinition) {
            $questMap[$questDefinition['id']] = $questDefinition['title'];
        }
        $userNick = null;
        if ($_POST['user_nick'] !== '') {
            $userNick = '%'.$_POST['user_nick'].'%';
        }
        $quests = $repository->searchQuests($getParam($_POST['quest_id']), $getParam($_POST['user_id']), $_POST['quest_state'], $userNick);
        if (count($quests) > 0) {
            echo '<table class="tbl">
                    <tr>
                        <td>ID</td>
                        <td class="tbltitle" valign="top">Spieler</td>
                        <td class="tbltitle" valign="top">Quest</td>
                        <td class="tbltitle" valign="top">Status</td>
                    </tr>';
            foreach ($quests as $data) {
                $style = in_array($data['state'], [\LittleCubicleGames\Quests\Workflow\QuestDefinition::STATE_AVAILABLE, \LittleCubicleGames\Quests\Workflow\QuestDefinition::STATE_IN_PROGRESS], true) ? ' style="color:#0f0"' : '';

                echo '<tr>
                        <td>'.$data['id'].'</td>';

                echo "<td class=\"tbldata\"$style ".mTT($data['user_nick'],nf($data['user_points'])." Punkte").">".cut_string($data['user_nick'],11)."</a></td>";
                echo "<td class=\"tbldata\"$style>".$questMap[$data['quest_data_id']]."</a></td>";
                echo "<td class=\"tbldata\"$style>".$data['state'] . '</td>';
//                echo "<td class=\"tbldata\">".edit_button("?page=$page&sub=$sub&action=edit&techlist_id=".$data['id'])."</td>";
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo 'Die Suche lieferte keine Resultate!';
        }
    }
}
