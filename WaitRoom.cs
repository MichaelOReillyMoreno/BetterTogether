using System;
using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;
//Si se vuelve a meter un usuario con otro nombre le dice q el profesor le cambio el equipo
//Dos personas con el mismo nombre jugarian la misma partida!!!!!!!!!!
public class WaitRoom : MonoBehaviour
{
	[SerializeField]
    private LanguageManager LanguageMan;
	
	[SerializeField]
    private CanvasGroup ChooseTeamPanel;

    private int currentTeam_onClient;
    private int desiredTeam_onClient;

    private int currentTeam_onServer;
    private int canPlay_onServer;
    private int canChooseTeam_onServer;

    private int id;

    private WWW download;

    private int[] serverAnswer;

    private string[] waitRoomTexts;

    private void Awake()
    {
        waitRoomTexts = LanguageMan.GeLanguageWaitRoom();
    }

    void Start()
    {
        ChooseTeamPanel.interactable = false;

        id = PlayerPrefs.GetInt("id");

        if (PlayerPrefs.GetInt("team") != 0)
        {
            currentTeam_onClient = desiredTeam_onClient = PlayerPrefs.GetInt("team");
            CanvasManager.Instance.ChangeTeamText(currentTeam_onClient);
        }

        StartToWaitGincana();
    }

    public void ChangeTeamByClient(int teamclicked)
    {
        if (desiredTeam_onClient != teamclicked)
        {
            if (desiredTeam_onClient == 0)
                CanvasManager.Instance.InfoClient(waitRoomTexts[0]);
            else
                CanvasManager.Instance.InfoClient(waitRoomTexts[1]);

            desiredTeam_onClient = teamclicked;
        }
    }

    private IEnumerator WaitToStartCo()
    {
        while (true)
        {
            download = new WWW("http://WEB.php?id=" + id + "&desiredTeam=" + desiredTeam_onClient + "&currentTeam=" + currentTeam_onClient);

            yield return download;

            TranslateServerAnswer();

            if (!CanvasManager.Instance.CheckErrors(currentTeam_onServer, download.error, 0))
            {
                if (currentTeam_onServer > 0 && currentTeam_onServer < 30)//El server cambio el equipo forzosamente
                {
                    yield return StartCoroutine(ServerForceChangeTeam());
                }
                else if (currentTeam_onServer == 42)//El cliente cambio al equipo deseado
                {
                    yield return StartCoroutine(ClientChangeTeamOnServer());
                }

                if (canChooseTeam_onServer == 1 && !ChooseTeamPanel.interactable)
                {
                    ChooseTeamPanel.interactable = true;
                }
                else if (canChooseTeam_onServer == 0 && ChooseTeamPanel.interactable)
                {
                    ChooseTeamPanel.interactable = false;
                }

                if (canPlay_onServer == 1)
                {
                    if (PlayerPrefs.GetInt("team") != 0)
                    {
                        StartCoroutine(StartGincana());
                        break;
                    }
                    else
                    {
                        CanvasManager.Instance.InfoClient(waitRoomTexts[2]);
                    }
                }
            }
            yield return new WaitForSeconds(10);
        }
    }

    private IEnumerator ServerForceChangeTeam()
    {
        currentTeam_onClient = desiredTeam_onClient = currentTeam_onServer;
        CanvasManager.Instance.ChangeTeamText(currentTeam_onClient);

        PlayerPrefs.SetInt("team", currentTeam_onClient);
        yield return StartCoroutine(CanvasManager.Instance.InfoClientChanges(waitRoomTexts[3], true));
    }

    private IEnumerator ClientChangeTeamOnServer()
    {
        CanvasManager.Instance.ChangeTeamText(desiredTeam_onClient);
        currentTeam_onClient = desiredTeam_onClient;

        PlayerPrefs.SetInt("team", currentTeam_onClient);
        yield return StartCoroutine(CanvasManager.Instance.InfoClientChanges(waitRoomTexts[4], true));
    }

    private IEnumerator StartGincana()
    {
        yield return StartCoroutine(CanvasManager.Instance.InfoClientChanges(waitRoomTexts[5], false));
        CanvasManager.Instance.LoadScene(2);
    }

    private void TranslateServerAnswer()
    {
        serverAnswer = Array.ConvertAll<string, int>(download.text.Split('_'), int.Parse);

        canPlay_onServer = serverAnswer[0];
        currentTeam_onServer = serverAnswer[1];
        canChooseTeam_onServer = serverAnswer[2];
    }

    public void StartToWaitGincana()
    {
        StartCoroutine(WaitToStartCo());
        CanvasManager.Instance.InfoClient(waitRoomTexts[6]);
    }
}