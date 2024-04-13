<?php

use Mateodioev\OllamaBot\Cache\UserCache;
use Mateodioev\OllamaBot\Db\MysqlDatabase;
use Mateodioev\OllamaBot\Events\{CancelCompletation, Chat, Models, PrivateTextListener, SetModel, Start, ViewCompletionDetails};
use Mateodioev\OllamaBot\Repository\MysqlUserRepository;
use Mateodioev\TgHandler\{Bot, Log};
use Revolt\EventLoop;

use function Mateodioev\OllamaBot\env;

require __DIR__ . '/vendor/autoload.php';

\Dotenv\Dotenv::createImmutable(__DIR__)->load();

// Create logger
$logStreams = new Log\BulkStream(
    new Log\TerminalStream(),
    (new Log\PhpNativeStream())->activate(env('LOG_DIR', __DIR__))
);
$logger = new Log\Logger($logStreams);

// Create bot
$bot = new Bot(env('BOT_TOKEN'), $logger);
$logStreams->add(new Log\BotApiStream($bot->getApi(), (int) env('BOT_LOG_CHANNEL_ID')));

$bot
    ->onEvent(Start::get())
    ->onEvent(Chat::get())
    ->onEvent(SetModel::get())
    ->onEvent(Models::get())
    ->onEvent(CancelCompletation::get())
    ->onEvent(new PrivateTextListener())
    ->onEvent(new ViewCompletionDetails());

$db = new MysqlDatabase('mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME') . ';charset=utf8mb4', env('DB_USER'), env('DB_PASS'));
UserCache::setRepo(new MysqlUserRepository($db));

$bot->longPolling(
    (int) env('BOT_POLLING_TIMEOUT', 60),
    (bool) env('BOT_POLLING_IGNORE_OLD_UPDATES', true),
    (bool) env('BOT_ASYNC', true)
);

EventLoop::run();
