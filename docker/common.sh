dotenv="$here/.env";

if [ ! -e "$dotenv" ]; then
  echo "error: file '$dotenv' does not exist!" >&2;
  echo "error: run 'php $here/initialize' to create it:" >&2;
  exit 1;
fi

. "$dotenv"

if [ -z "$COMPOSE_BINARY" ]; then
  COMPOSE_BINARY='docker-compose';
fi
