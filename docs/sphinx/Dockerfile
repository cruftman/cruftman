FROM keimlink/sphinx-doc
USER 0
RUN apk add --no-cache git curl php7
USER 1000
COPY --chown=1000:1000 requirements.txt ./
RUN . .venv/bin/activate && \
    python -m pip install -U pip setuptools && \
    python -m pip install --requirement requirements.txt
EXPOSE 8000
WORKDIR "/home/python/docs"
CMD ["sphinx-autobuild", "--host", "0.0.0.0", "--port", "8000", "sphinx", "build/html"]

# vim: syntax=dockerfile:
