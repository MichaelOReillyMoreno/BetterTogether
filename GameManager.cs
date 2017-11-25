using EasyAR;
using System;
using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class GameManager : MonoBehaviour
 {
	[SerializeField]
    private string KeyAr;
	
	[SerializeField]
    private InventoryTest InvTest;
	
	[SerializeField]
    private TestInfo InfoTests;

    public int CurrentTest { get; set; }

    private int team;
    private int teamHasWon;

    private WWW download;

    private int[] testsState_onServer;
    private int[] testsState_onClient;

    private bool isUpdatingServer;
    private bool aTeamWon;

    void Start ()
    {
        CurrentTest = 99;
        teamHasWon = 0;
        team = PlayerPrefs.GetInt("team");
        testsState_onClient = new int[] { 0, 0, 0, 0, 0, 0, 0, 0 };
        CheckExistTeam();
        StartEasyAr();
    }

    public void SetTest(int nTest, int testValue)
    {
        if ((CurrentTest == nTest) && (testsState_onClient[nTest] < testValue) && !InfoTests.IsOpen)
        {
            testsState_onClient[nTest] = testValue;
            InvTest.ChangeTestState(nTest, testValue);

            InfoTests.OpenTest(nTest);
        }
    }

    public int GetTestState(int nTest)
    {
        return testsState_onClient[nTest];
    }

    private IEnumerator testsUpdaterCo()
    {
        while (true)
        {
            download = new WWW("http://WEB.php?team=" + team );

            yield return download;

            TranslateServerAnswer();

            if (!CanvasManager.Instance.CheckErrors(testsState_onServer[0], download.error, (testsState_onClient.Length - testsState_onServer.Length)))
            {
                for (int n = 0; n < testsState_onServer.Length; n++)
                {
                    if (testsState_onClient[n] < testsState_onServer[n])//si otro jugador del equipo ha superado una prueba
                    {
                        UpdateTestClient(n);
                    }
                    else if (testsState_onClient[n] > testsState_onServer[n])//si has realizado una prueba que no ha sido actualizada en el servidor
                    {
                        yield return StartCoroutine(UpdateTestServer(1 + n, testsState_onClient[n]));
                    }
                }

                if (!aTeamWon)
                {
                    CheckWin();

                    if (teamHasWon != 0)
                    {
                        CanvasManager.Instance.WinTeamText(teamHasWon);
                        aTeamWon = true;
                    }
                }
            }
            yield return new WaitForSeconds(10);
        }
    }

    private void UpdateTestClient(int nTest)
    {
        testsState_onClient[nTest] = testsState_onServer[nTest];
        InvTest.ChangeTestState(nTest, testsState_onClient[nTest]);
    }

    private IEnumerator UpdateTestServer(int nTest, int testValue)
    {
        while(isUpdatingServer)
            yield return new WaitForSeconds(2);

        isUpdatingServer = true;
        download = new WWW("http://WEB.php?team=" + team + "&nTest=" + nTest + "&testValue=" + testValue);
        yield return download;

        CanvasManager.Instance.CheckErrors(Convert.ToInt32(download.text), download.error, 0);
        isUpdatingServer = false;
    }

    private void CheckWin()
    {
        bool isPossibleWinState = true;

        for (int n = 0; n < testsState_onClient.Length; n++)
        {
            if (testsState_onClient[n] != 3)
            {
                isPossibleWinState = false;
                break;
            }
        }

        if (isPossibleWinState)
        {
            StartCoroutine(UpdateWinStateServer());
        }
    }

    private IEnumerator UpdateWinStateServer()
    {
        WWW download = new WWW("http://WEB.php?team=" + team);
        yield return download;
    }

    private void TranslateServerAnswer()
    {
        if (Int32.TryParse(download.text.Substring(0, 2), out teamHasWon))

        testsState_onServer = Array.ConvertAll<string, int>(download.text.Substring(3, download.text.Length - 3).Split('_'), int.Parse);
    }

    private void CheckExistTeam()
    {
        if (PlayerPrefs.GetInt("team") != 0)
        {
            team = PlayerPrefs.GetInt("team");
            StartCoroutine(testsUpdaterCo());
        }
        else
        {
            CanvasManager.Instance.Logout();
        }
    }


    private void StartEasyAr()
    {
        ARBuilder.Instance.InitializeEasyAR(KeyAr);
        ARBuilder.Instance.EasyBuild();
        StartCoroutine(ImageTargetBehaviour.StartToTrack());
    }

}
