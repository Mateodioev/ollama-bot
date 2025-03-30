# Ollama telegram bot

This bot helps you to interact with [ollama models](https://ollama.ai/library)

https://github.com/Mateodioev/ollama-bot/assets/68271130/6cbec742-994b-4f79-85cf-804ec95663ad

## Commands
If you want to generate a completion just send a private message to the bot or if you want to use in a public group use the command `/chat` followed by the text of your choice. Other commands:

- `/setmodel` Change your current model
- `/start` 

## Installation

### Requirements:
- Docker

### Steps

1. Clone this repository
```bash
git clone https://github.com/Mateodioev/ollama-bot.git
cd ollama-bot
```

2. Setup docker
```bash
docker compose up --build
```

3. Install ollama models
```bash
docker compose exec -T ollama ollama pull <model name>
```


**Note**
> If your default model is different to `codellama` for example `llama3`, edit the file `src/Middlewares/FindUserOrRegister.php` line 17 and change to you default model.

See ollama docs in https://github.com/jmorganca/ollama
