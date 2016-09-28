FROM python:2
RUN pip install Flask
RUN mkdir /web_app
ADD . /web_app
ENV FLASK_APP /web_app/app.py
ENTRYPOINT flask run --host=0.0.0.0 
