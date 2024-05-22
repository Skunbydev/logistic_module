# Projeto `logistic_module`

O `logistic_module` é um projeto desenvolvido e mantido por **Luiz Felipe Santos**, com o objetivo principal de **praticar e aprimorar habilidades em PHP e Ajax**. Ele consiste em um sistema modularizado com foco em gerenciamento de pedidos, envios, produtos, estoque e lista de clientes.

---

## Motivação

A motivação por trás do desenvolvimento deste projeto foi a busca por **aprimoramento profissional** e a oportunidade de aplicar e aprofundar conhecimentos em **programação web**, especialmente nas tecnologias PHP e Ajax.

---

## Como Utilizar

Para utilizar o `logistic_module`, siga os passos abaixo:

1. **Configuração do Servidor Local:**

   - Utilize um servidor local como **XAMPP** ou **Laragon** para emular um ambiente de desenvolvimento.

2. **Instalação do Projeto:**

   - Copie os arquivos do projeto para o diretório padrão do servidor (geralmente `htdocs` no XAMPP).

3. **Baixe as dependências adicionais no projeto:**
   - `composer require datatables/datatables`
   - `https://jquery.com/download/`
   
   **OBS: os diretórios padrões do projeto estão apontando para: includes/plugin**
4. **Acesso ao Sistema:**

   - Acesse o sistema através do navegador.

5. **Login:**
   - Utilize as credenciais padrão fornecidas no projeto (`luiz.felipe@skunby.com` / `teste123`) ou crie suas próprias credenciais de acesso.

---

## Versão Atual: 0.2

### Data de Lançamento: 15/05/2024

### Novidades e Funcionalidades

#### Módulo de Estoque

- **Funcionalidades:**
  - Edição de itens.
  - Remoção de itens.

#### Módulo de Lista de Clientes

- **Funcionalidades:**
  - Edição de clientes.
  - Remoção de clientes.
  - Adição de novos clientes.

### Data de Lançamento: 17/05/2024

#### Módulo de Pedidos

- **Integração Direta:**
  - Sincronização com a lista de clientes e o estoque.
- **Funcionalidades:**
  - **Adição de Novo Pedido:**
    - Os pedidos são atualizados em tempo real com o estoque. Por exemplo, se forem adicionadas 2 peças novas de carro, elas aparecerão imediatamente na tela de criação de pedidos.
  - **Edição de Pedidos:**
    - Qualquer alteração nos dados do cliente será refletida na aba de cliente na tela de pedidos.
  - **Remoção de Pedidos:**
    - Campos editáveis para atualização/modificação de dados do cliente diretamente na tela de pedidos.

---

Este projeto representa uma grande oportunidade de aprendizado e desenvolvimento, combinando o uso de PHP e Ajax em um sistema funcional e interativo. Experimente e explore todas as funcionalidades oferecidas pelo `logistic_module`!
