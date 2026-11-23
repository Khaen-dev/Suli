<?php

// Adatbázis
function queryAll($conn, $sql, $params = [])
{
    $statement = $conn->prepare($sql);
    if (count($params) > 0) {
        $statement->bind_param(str_repeat("s", count($params)), ...$params);
    }
    $statement->execute();
    return $statement->get_result()->fetch_all(MYSQLI_ASSOC);
}

function queryOne($conn, $sql, $params = [])
{
    $rows = queryAll($conn, $sql, $params);
    if (count($rows) === 0) {
        return null;
    }
    return $rows[0];
}

function runQuery($conn, $sql, $params = [])
{
    $statement = $conn->prepare($sql);
    if (count($params) > 0) {
        $statement->bind_param(str_repeat("s", count($params)), ...$params);
    }
    $statement->execute();
    return $conn->insert_id;
}

// Bejelentkezés
function getLoggedInUserId()
{
    if (!isset($_COOKIE['id'])) {
        header("Location: login.php");
        exit();
    }
    return (int)$_COOKIE['id'];
}

// Előfizetés
function getSubscription($conn, $userId)
{
    $subscription = queryOne($conn, "
        SELECT user_subscriptions.expires, pricing.*
        FROM user_subscriptions
        JOIN pricing ON pricing.id = user_subscriptions.subscription
        WHERE user_subscriptions.userid = ?
    ", [$userId]);

    if ($subscription === null) {
        return [
            'has_subscription' => false,
            'is_active'        => false,
            'name'             => 'Nincs előfizetés',
            'expires'          => '-',
            'store_count'      => 0,
            'milestone_limit'  => 0,
            'staff_limit'      => 0,
            'allow_shared_db'  => 0,
            'allow_events'     => 0,
        ];
    }

    $subscription['has_subscription'] = true;
    $subscription['is_active'] = strtotime($subscription['expires']) >= time();
    return $subscription;
}

// Rangok
const ROLE_NAMES = [
    'tulajdonos' => 'Tulajdonos',
    'menedzser'  => 'Menedzser',
    'szerkeszto' => 'Szerkesztő',
    'pultos'     => 'Pultos',
];

const ROLE_PERMISSIONS = [
    'tulajdonos' => ['points', 'milestones', 'events', 'messages', 'staff', 'settings'],
    'menedzser'  => ['points', 'milestones', 'events', 'messages', 'staff', 'settings'],
    'szerkeszto' => ['points', 'milestones', 'events', 'messages'],
    'pultos'     => ['points'],
];

function getUserRole($conn, $store, $userId)
{
    if ((int)$store['owner_userid'] === $userId) {
        return 'tulajdonos';
    }

    $staff = queryOne($conn, "SELECT role FROM store_staff WHERE store_id = ? AND user_id = ?", [$store['id'], $userId]);
    if ($staff === null) {
        return null;
    }
    return $staff['role'];
}

function hasPermission($role, $permission)
{
    return in_array($permission, ROLE_PERMISSIONS[$role]);
}

// Pontok helye: a bolt, vagy ha csoportban van, a csoport
function getPointOwner($store)
{
    if ($store['group_id'] !== null) {
        return ['column' => 'group_id', 'id' => $store['group_id']];
    }
    return ['column' => 'store_id', 'id' => $store['id']];
}
