Envoi aims to ease the use and documentation of environment variables (env
vars) in PHP applications.

Envoi features:

- a Yaml schema to describe the env vars that may be used to configure an
  application

- tools to validate env vars against a schema

- a tool to assist in the population of a `.env` file

- a tool which converts a schema to markdown

Envoi sports a console command which validates a `.env` file against a schema
(by convention, `.env.yaml`).  It also provides checkers that, when invoked
early in the start-up phase of an application, will halt an application which
doesn't have a complete and valid set of env vars.

