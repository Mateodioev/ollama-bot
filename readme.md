# Ollama telegram bot

This bot helps you to interact with [ollama models](https://ollama.ai/library)

![How to use](https://i.imgur.com/fHfhjb2.mp4)

## Commands
If you want to generate a completion just send a private message to the bot or if you want to use in a public group use the command `/chat` followed by the text of your choice. Other commands:

- `/setmodel` Change your current model
- `/start` 

## Installation

### Requirements:
- \>= PHP 8.2
- Mysql
- Access to ollama api

### Steps

1. Clone this repository
```bash
git clone https://github.com/Mateodioev/ollama-bot.git
cd ollama-bot
```

2. Install dependencies
```bash
composer install --optimize-autoloader --no-interaction --no-dev
```

3. Setup your database mysql with file `db/main.sql`

4. Create an edit .env file
```bash
cp .env.example .env
vim .env # Or use your favorite editor
```

5. Run the bot
```bash
php index.php
```


**Note**
> If your default model is diferent to `codellama` for example `llama2`, edit the file `src/Events/Middlewares.php` line 13

See ollama docs in https://github.com/jmorganca/ollama
